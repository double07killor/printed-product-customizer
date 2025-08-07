jQuery(function($){
    console.log('Printed Product Customizer admin loaded');

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
        var $key = $label.closest('.fpc-repeatable-row').find('.fpc-key-field');
        if(!$key.data('edited')){
            $key.val($label.val().toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_|_$/g,''));
        }
    });
    $(document).on('change', '.fpc-key-field', function(){
        $(this).data('edited', true);
    });

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
});
