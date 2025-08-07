jQuery(function($){
    console.log('Printed Product Customizer admin loaded');

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

    function addRow(container){
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
        container.append(template);
        initTagInputs(template.find('.fpc-tag-input'));
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

    initTagInputs($('.fpc-tag-input'));
});
