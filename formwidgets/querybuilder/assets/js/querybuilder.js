/*
 * Star rating class
 */
+function ($) {
    "use strict";

    if ($.ti === undefined) $.ti = {}

    if ($.ti.tastyQueryBuilder === undefined)
        $.ti.tastyQueryBuilder = {}

    var tastyQueryBuilder = function (element, options) {
        this.$el = $(element)

        this.options = options

        this.init()
    }

    tastyQueryBuilder.prototype.constructor = tastyQueryBuilder

    tastyQueryBuilder.prototype.init = function () {
        
        this.$selectElement = this.$el.find('.querybuilder-options-select');
        this.$selectElement.on('change', $.proxy(this.onOptionChange, this));
        
        this.$inputElement = this.$el.find('textarea');
        
        this.makeBuilder();
    }
    
    tastyQueryBuilder.prototype.makeBuilder = function() { 
        
        let opts = JSON.parse(JSON.stringify(this.options));
        opts.filters = opts.filters[this.$selectElement[0].value].filters;
                
        this.$builderElement = this.$el.find('.querybuilder')
        this.$builderElement.queryBuilder(opts);
        
        var inputElement = this.$inputElement;
        var builderElement = this.$builderElement[0].queryBuilder;
        builderElement.on('rulesChanged', function(){
            inputElement.val(JSON.stringify(builderElement.getRules()));   
        });

    }
    
    tastyQueryBuilder.prototype.onOptionChange = function() {
        this.$builderElement[0].queryBuilder.destroy();
        this.makeBuilder();
    }

    // QUERY BUILDER PLUGIN DEFINITION
    // ============================

    tastyQueryBuilder.DEFAULTS = {
        filters: []
    }

    var old = $.fn.tastyQueryBuilder

    $.fn.tastyQueryBuilder = function (option) {
        var args = Array.prototype.slice.call(arguments, 1),
            result = undefined

        this.each(function () {
            var $this = $(this)
            var data = $this.data('ti.tastyQueryBuilder')
            var options = $.extend({}, tastyQueryBuilder.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('ti.tastyQueryBuilder', (data = new tastyQueryBuilder(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.tastyQueryBuilder.Constructor = tastyQueryBuilder

    // MEDIA MANAGER NO CONFLICT
    // =================

    $.fn.tastyQueryBuilder.noConflict = function () {
        $.fn.tastyQueryBuilder = old
        return this
    }

    // MEDIA MANAGER DATA-API
    // ===============

    $(document).render(function () {
        $('[data-control="query-builder"]').tastyQueryBuilder()
    })

}(window.jQuery);
