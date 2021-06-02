
+function ($) {
    "use strict"
    
    var $modelEl = $('#form-field-querybuilder-builderjson-type');
    var $initialValue = $modelEl.val();
    
    var checkContexts = function() {
        var $context = $modelEl.val();
        $('.repeater-items select option')
        .each(function($idx, $el) {
            let $val = JSON.parse($el.value);
            if (!$val.contexts.includes($context))
                $el.disabled = true;    
        });
    };

    $modelEl.on('change', checkContexts);
    $(window).on('repeaterItemAdded', checkContexts);
    
}(window.jQuery)