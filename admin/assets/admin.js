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
        }
        container.append(template);
        updateFilamentOptions(template);
        updateGroupTitle(template);
        $(document.body).trigger('wc-enhanced-select-init');
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
        $blacklist.html(blacklistOptions).val(blacklistVal).trigger('change');

        var filterFn = function(slug){ return blacklist.indexOf(slug) === -1; };
        var filteredOptions = buildOptions(filterFn);

        var $default = $row.find('.fpc-default-filament');
        var defaultVal = $default.val();
        $default.html('<option value=""></option>'+filteredOptions);
        if(defaultVal && $default.find('option[value="'+defaultVal+'"]').length){
            $default.val(defaultVal);
        }
        $default.trigger('change');

        var $whitelist = $row.find('.fpc-filament-whitelist');
        var whitelistVal = $whitelist.val() || [];
        $whitelist.html(filteredOptions);
        $whitelist.val(whitelistVal.filter(function(v){ return $whitelist.find('option[value="'+v+'"]').length; })).trigger('change');
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
        $container.append(template);
        updateFilamentOptions(template);
        updateGroupTitle(template);
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

    $(document).on('change', '.fpc-materials, .fpc-filament-blacklist', function(){
        updateFilamentOptions($(this).closest('.fpc-repeatable-row'));
    });

    $('.fpc-repeatable-row').each(function(){
        updateFilamentOptions($(this));
        updateGroupTitle($(this));
    });
    $(document.body).trigger('wc-enhanced-select-init');

    $(document).on('click', '.fpc-group-toggle', function(e){
        e.preventDefault();
        $(this).next('.fpc-group-fields').slideToggle();
    });

    $('.fpc-additional-rules-toggle').on('click', function(e){
        e.preventDefault();
        initAdditionalGroupRules(window.fpcAdditionalGroupRules || {});
        $('#fpc-additional-rules').toggle();
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

    $('#fpc-save-3mf').on('click', function(e){
        e.preventDefault();
        var btn = $(this);
        var panel = $('#fpc_3mf_mapping_panel');
        var filesInput = panel.find('input[name="fpc_3mf_files[]"]')[0];
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
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp){
                btn.prop('disabled', false);
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
            error: function(){
                btn.prop('disabled', false);
                panel.find('.fpc-save-notice').text('Error').show();
            }
        });
    });
    initTagInputs($('.fpc-tag-input'));
});
