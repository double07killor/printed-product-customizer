jQuery(function($){

    function initTagInputs($inputs){
        $inputs.each(function(){
            var $input = $(this);
            var options = $input.data('options') || [];
            if(typeof options === 'string'){
                try { options = JSON.parse(options); } catch(e){ options = options.split(','); }
            }
            function split(val){ return val.split(/,\s*/); }
            function extractLast(term){ return split(term).pop(); }
            $input.on('keydown', function(event){
                if(event.keyCode === $.ui.keyCode.TAB && $input.autocomplete('instance') && $input.autocomplete('instance').menu.active){
                    event.preventDefault();
                }
            }).autocomplete({
                minLength:0,
                source:function(request, response){
                    response($.ui.autocomplete.filter(options, extractLast(request.term)));
                },
                focus:function(){ return false; },
                select:function(event, ui){
                    var terms = split(this.value);
                    terms.pop();
                    terms.push(ui.item.value);
                    terms.push('');
                    this.value = terms.join(', ');
                    return false;
                }
            });
        });
    }

    function addRow(container, data){
        var template = container.find('.fpc-template').first().clone();
        template.removeClass('fpc-template').show();
        var index = container.find('.fpc-repeatable-row').length - 1;
        template.find(':input').each(function(){
            var name = $(this).attr('name');
            if(name){
                name = name.replace('__INDEX__', index);
                $(this).attr('name', name);
            }
        });
        if(data){
            template.find('.fpc-body-field').val(data.body || '');
            template.find('.fpc-subgroup-field').val(data.subgroup || '');
            template.find('.fpc-no-visualization-field').prop('checked', data.no_visualization === '1');
            template.find('.fpc-non-exported-field').prop('checked', data.non_exported === '1');
            template.find('.fpc-finish-field').val(data.finish || '');
        } else {
            template.find('.fpc-materials').val(['PETG']);
            template.find('.fpc-default-filament').val('psm-m-bk-petg');
        }
        container.append(template);
        updateFilamentOptions(template);
        updateGroupTitle(template);
        toggleExemptFilaments(template);
        $(document.body).trigger('wc-enhanced-select-init');
    }

    function refreshEnhancedSelect($el){
        if ($.fn.selectWoo && $el && $el.length) {
            if ($el.data('select2')) {
                $el.selectWoo('destroy');
            }
            $el.selectWoo();
        }
    }

    function updateFilamentOptions($row){
        var inventory = window.fpcFilamentInventory || {};
        var materials = $row.find('.fpc-materials').val() || [];
        var blacklist = $row.find('.fpc-filament-blacklist').val() || [];

        function buildOptions(filter){
            var html = '';
            $.each(inventory, function(slug, data){
                if(materials.length && materials.indexOf(data.material) === -1){
                    return;
                }
                if(filter && !filter(slug)){
                    return;
                }
                html += '<option value="'+slug+'">'+slug+'</option>';
            });
            return html;
        }

        var blacklistOptions = buildOptions();
        var $blacklist = $row.find('.fpc-filament-blacklist');
        var blacklistVal = $blacklist.val() || [];
        $blacklist.html(blacklistOptions).val(blacklistVal);

        var filterFn = function(slug){ return blacklist.indexOf(slug) === -1; };
        var filteredOptions = buildOptions(filterFn);

        var $default = $row.find('.fpc-default-filament');
        var defaultVal = $default.val();
        $default.html('<option value=""></option>'+filteredOptions);
        if(defaultVal && $default.find('option[value="'+defaultVal+'"]').length){
            $default.val(defaultVal);
        }

        var $whitelist = $row.find('.fpc-filament-whitelist');
        var whitelistVal = $whitelist.val() || [];
        $whitelist.html(filteredOptions);
        $whitelist.val(whitelistVal.filter(function(v){ return $whitelist.find('option[value="'+v+'"]').length; }));

        var $exempt = $row.find('.fpc-exempt-filaments');
        var exemptVal = $exempt.val() || [];
        $exempt.html(filteredOptions);
        $exempt.val(exemptVal.filter(function(v){ return $exempt.find('option[value="'+v+'"]').length; }));

        refreshEnhancedSelect($blacklist);
        refreshEnhancedSelect($default);
        refreshEnhancedSelect($whitelist);
        refreshEnhancedSelect($exempt);

        $blacklist.trigger('change');
        $default.trigger('change');
        $whitelist.trigger('change');
        $exempt.trigger('change');
    }

    function updateGroupTitle($row){
        var label = $row.find('.fpc-generate-key').val();
        if(!label){
            label = $row.data('default-title') || 'Group';
        }
        $row.find('.fpc-group-title').text(label);
    }

    function initAdditionalGroupRules(data){
        var $container = $('#fpc-additional-rules');
        if($container.data('initialized')){
            return;
        }
        var template = $('.fpc-repeatable-container .fpc-template').first().clone();
        template.removeClass('fpc-template').addClass('fpc-additional-row').show();
        var label = $container.data('label') || 'Additional Group Rules';
        template.attr('data-default-title', label);
        template.find('.fpc-group-title').text(label);
        template.find('.fpc-repeatable-remove').remove();
        template.find('.fpc-additional-fee-field').show();
        template.find(':input').each(function(){
            var name = $(this).attr('name');
            if(name){
                name = name.replace('fpc_filament_groups[__INDEX__]', 'fpc_additional_group_rules');
                $(this).attr('name', name);
                var match = name.match(/\[([^\]]+)\]$/);
                var key = match ? match[1] : null;
                if(key && typeof data[key] !== 'undefined'){
                    if($(this).is(':checkbox')){
                        $(this).prop('checked', data[key] == 1);
                    } else if($(this).is('select[multiple]')){
                        $(this).val(data[key]);
                    } else {
                        $(this).val(data[key]);
                    }
                }
            }
        });
        if(!data || $.isEmptyObject(data)){
            template.find('.fpc-materials').val(['PETG']);
            template.find('.fpc-default-filament').val('psm-m-bk-petg');
        }
        $container.append(template);
        updateFilamentOptions(template);
        updateGroupTitle(template);
        toggleExemptFilaments(template);
        $(document.body).trigger('wc-enhanced-select-init');
        $container.data('initialized', true);
    }

    $('.fpc-repeatable-add').on('click', function(e){
        e.preventDefault();
        var container = $(this).closest('.fpc-repeatable-wrapper').find('.fpc-repeatable-container');
        addRow(container);
    });

    $(document).on('click', '.fpc-repeatable-remove', function(e){
        e.preventDefault();
        $(this).closest('.fpc-repeatable-row').remove();
    });

    $(document).on('keyup change', '.fpc-generate-key', function(){
        var $label = $(this);
        var $row = $label.closest('.fpc-repeatable-row');
        var $key = $row.find('.fpc-key-field');
        if(!$key.data('edited')){
            $key.val($label.val().toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_|_$/g,''));
        }
        updateGroupTitle($row);
    });
    $(document).on('change', '.fpc-key-field', function(){
        $(this).data('edited', true);
    });

    $(document).on('change select2:select select2:unselect', '.fpc-materials, .fpc-filament-blacklist', function(){
        updateFilamentOptions($(this).closest('.fpc-repeatable-row'));
    });

    function toggleExemptFilaments($row){
        var $all = $row.find('.fpc-exempt-all');
        var $field = $row.find('.fpc-exempt-filaments-field');
        var $select = $row.find('.fpc-exempt-filaments');
        if($all.is(':checked')){
            $select.prop('disabled', true);
            $field.hide();
        } else {
            $select.prop('disabled', false);
            $field.show();
        }
    }

    $(document).on('change', '.fpc-exempt-all', function(){
        toggleExemptFilaments($(this).closest('.fpc-repeatable-row'));
    });

    $('.fpc-repeatable-row').each(function(){
        updateFilamentOptions($(this));
        updateGroupTitle($(this));
        toggleExemptFilaments($(this));
        $(this).find('.fpc-group-fields').hide();
    });
    $(document.body).trigger('wc-enhanced-select-init');

    $(document).on('click', '.fpc-group-toggle', function(e){
        e.preventDefault();
        $(this).closest('.fpc-repeatable-row').find('.fpc-group-fields').first().slideToggle();
    });

    $('.fpc-additional-rules-toggle').on('click', function(e){
        e.preventDefault();
        initAdditionalGroupRules(window.fpcAdditionalGroupRules || {});
        var $container = $('#fpc-additional-rules');
        $container.slideToggle(function(){
            if($container.is(':visible')){
                $(document.body).trigger('wc-enhanced-select-init');
            }
        });
    });

    if(window.fpcAdditionalGroupRules && Object.keys(window.fpcAdditionalGroupRules).length){
        initAdditionalGroupRules(window.fpcAdditionalGroupRules);
    }

    function addPriceRow(container){
        var template = container.find('.fpc-template').first().clone();
        template.removeClass('fpc-template').show();
        var index = container.find('.fpc-price-row').length - 1;
        template.find(':input, select').each(function(){
            var name = $(this).attr('name');
            if(name){
                name = name.replace('__PRICE_INDEX__', index);
                $(this).attr('name', name);
            }
        });
        container.append(template);
    }

    $(document).on('click', '.fpc-price-add', function(e){
        e.preventDefault();
        var container = $(this).closest('.form-field').find('.fpc-price-adjust-container');
        addPriceRow(container);
    });
    $(document).on('click', '.fpc-price-remove', function(e){
        e.preventDefault();
        $(this).closest('.fpc-price-row').remove();
    });


    function addOptionRow(container){
        var template = container.find('.fpc-template').first().clone();
        template.removeClass('fpc-template').show();
        var index = container.find('.fpc-option-row').length - 1;
        template.find(':input, select').each(function(){
            var name = $(this).attr('name');
            if(name){
                name = name.replace('__OPT_INDEX__', index);
                $(this).attr('name', name);
            }
        });
        container.append(template);
    }

    $(document).on('click', '.fpc-option-add', function(e){
        e.preventDefault();
        var container = $(this).closest('.fpc-option-wrapper').find('.fpc-option-container');
        addOptionRow(container);
    });

    $(document).on('click', '.fpc-option-remove', function(e){
        e.preventDefault();
        $(this).closest('.fpc-option-row').remove();
    });

    function addDesignRow(container){
        var template = container.find('.fpc-template').first().clone();
        template.removeClass('fpc-template').show();
        var index = container.find('.fpc-design-row').length - 1;
        template.find(':input, select').each(function(){
            var name = $(this).attr('name');
            if(name){
                name = name.replace('__DESIGN_INDEX__', index);
                $(this).attr('name', name);
            }
        });
        container.append(template);
    }

    $(document).on('click', '.fpc-design-add', function(e){
        e.preventDefault();
        var container = $(this).closest('.fpc-design-wrapper').find('.fpc-design-container');
        addDesignRow(container);
    });

    $(document).on('click', '.fpc-design-remove', function(e){
        e.preventDefault();
        $(this).closest('.fpc-design-row').remove();
    });

    $('#fpc-save-3mf').on('click', function(e){
        e.preventDefault();
        var btn = $(this);
        var panel = $('#fpc_3mf_mapping_panel');
        var filesInput = panel.find('input[name="fpc_3mf_files[]"]')[0];
        console.log('fpc-save-3mf: clicked');
        console.log('fpc-save-3mf: files selected', filesInput && filesInput.files ? filesInput.files.length : 0);
        var formData = new FormData();
        formData.append('action', 'fpc_save_3mf_files');
        formData.append('post_id', $('#post_ID').val());
        if(filesInput && filesInput.files){
            for(var i=0;i<filesInput.files.length;i++){
                formData.append('fpc_3mf_files[]', filesInput.files[i]);
            }
        }
        btn.prop('disabled', true);
        panel.find('.fpc-save-notice').hide();
        console.log('fpc-save-3mf: sending ajax');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp){
                btn.prop('disabled', false);
                console.log('fpc-save-3mf: response', resp);
                if(resp.success){
                    var container = panel.find('.fpc-repeatable-container');
                    container.find('.fpc-repeatable-row').not('.fpc-template').remove();
                    $.each(resp.data.assignments, function(i, as){
                        addRow(container, as);
                    });
                    panel.find('.fpc-save-notice').text(resp.data.message).show();
                } else {
                    panel.find('.fpc-save-notice').text(resp.data && resp.data.message ? resp.data.message : 'Error').show();
                }
            },
            error: function(err){
                btn.prop('disabled', false);
                console.error('fpc-save-3mf: ajax error', err);
                panel.find('.fpc-save-notice').text('Error').show();
            }
        });
    });

    $('#fpc-update-bodies').on('click', function(e){
        e.preventDefault();
        var btn = $(this);
        var panel = $('#fpc_3mf_mapping_panel');
        var formData = new FormData();
        formData.append('action', 'fpc_update_bodies');
        formData.append('post_id', $('#post_ID').val());
        console.log('fpc-update-bodies: clicked');
        btn.prop('disabled', true);
        panel.find('.fpc-save-notice').hide();
        console.log('fpc-update-bodies: sending ajax');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp){
                btn.prop('disabled', false);
                console.log('fpc-update-bodies: response', resp);
                if(resp.success){
                    var container = panel.find('.fpc-repeatable-container');
                    container.find('.fpc-repeatable-row').not('.fpc-template').remove();
                    $.each(resp.data.assignments, function(i, as){
                        addRow(container, as);
                    });
                    panel.find('.fpc-save-notice').text(resp.data.message).show();
                } else {
                    panel.find('.fpc-save-notice').text(resp.data && resp.data.message ? resp.data.message : 'Error').show();
                }
            },
            error: function(err){
                btn.prop('disabled', false);
                console.error('fpc-update-bodies: ajax error', err);
                panel.find('.fpc-save-notice').text('Error').show();
            }
        });
    });
    initTagInputs($('.fpc-tag-input'));
});
