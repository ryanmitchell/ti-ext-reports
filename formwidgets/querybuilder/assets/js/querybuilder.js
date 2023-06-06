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

        this.$value = {model: ''};
        if (this.$el.data('value')) {
            this.$value = this.$el.data('value');
        }

        this.makeBuilder();

        if (this.$value.model)
            this.$selectElement.val(this.$value.model).trigger('change');

    }

    tastyQueryBuilder.prototype.makeBuilder = function () {

        let opts = JSON.parse(JSON.stringify(this.options));
        opts.filters = opts.filters[this.$selectElement[0].value].filters;
        opts.plugins = [];
        opts.rules = this.$value.model == this.$selectElement[0].value ? this.$value.rules : false;

        var $builderElement;
        this.$builderElement = $builderElement = this.$el.find('.querybuilder');

        this.$builderElement.on('afterUpdateRuleValue.queryBuilder', function (e, rule) {
            if (rule.filter.plugin === 'datepicker') {
                rule.$el.find('.rule-value-container input').datepicker('update');
            }
        });

        this.$builderElement.queryBuilder(opts);

        var inputElement = this.$inputElement;
        var builderElement = this.$builderElement[0].queryBuilder;

        this.$ignoreNextEvent = false;
        builderElement.on('beforeDestroy', $.proxy(function () {
            this.$ignoreNextEvent = true;
        }, this));

        builderElement.$el.removeClass('form-inline').addClass('form-block');

        builderElement.on('rulesChanged', $.proxy(this.onRulesChanged, this));
        this.onRulesChanged();

    }

    tastyQueryBuilder.prototype.onRulesChanged = function () {
        let val = {
            model: this.$selectElement.val(),
            rules: (this.$ignoreNextEvent ? [] : this.$builderElement.queryBuilder('getRules'))
        };
        this.$inputElement.val(JSON.stringify(val));
    }

    tastyQueryBuilder.prototype.onOptionChange = function () {
        this.$builderElement[0].queryBuilder.destroy();
        this.makeBuilder();
    }

    // QUERY BUILDER PLUGIN DEFINITION
    // ============================

    tastyQueryBuilder.DEFAULTS = {
        filters: [],
        templates: {
            group: '\
<div id="{{= it.group_id }}" class="rules-group-container"> \
  <div class="rules-group-header"> \
    <div class="btn-group pull-right group-actions"> \
      <button type="button" class="btn btn-link fw-bold text-decoration-none text-success" data-add="rule"> \
        <i class="{{= it.icons.add_rule }}"></i> {{= it.translate("add_rule") }} \
      </button> \
      {{? it.settings.allow_groups===-1 || it.settings.allow_groups>=it.level }} \
        <button type="button" class="btn btn-link fw-bold text-decoration-none text-success" data-add="group"> \
          <i class="{{= it.icons.add_group }}"></i> {{= it.translate("add_group") }} \
        </button> \
      {{?}} \
      {{? it.level>1 }} \
        <button type="button" class="btn btn-link fw-bold text-decoration-none text-danger" data-delete="group"> \
          <i class="{{= it.icons.remove_group }}"></i> {{= it.translate("delete_group") }} \
        </button> \
      {{?}} \
    </div> \
    <div class="btn-group btn-group-sm group-conditions"> \
      {{~ it.conditions: condition }} \
        <label class="btn btn-outline-primary"> \
          <input type="radio" name="{{= it.group_id }}_cond" value="{{= condition }}"> {{= it.translate("conditions", condition) }} \
        </label> \
      {{~}} \
    </div> \
    {{? it.settings.display_errors }} \
      <div class="error-container"><i class="{{= it.icons.error }}"></i></div> \
    {{?}} \
  </div> \
  <div class=rules-group-body> \
    <div class=rules-list></div> \
  </div> \
</div>',
            rule: '\
<div id="{{= it.rule_id }}" class="rule-container"> \
  <div class="rule-header"> \
    <div class="btn-group pull-right rule-actions"> \
      <button type="button" class="btn btn-link fw-bold text-decoration-none text-danger" data-delete="rule"> \
        <i class="{{= it.icons.remove_rule }}"></i> {{= it.translate("delete_rule") }} \
      </button> \
    </div> \
  </div> \
  {{? it.settings.display_errors }} \
    <div class="error-container"><i class="{{= it.icons.error }}"></i></div> \
  {{?}} \
  <div class="rule-filter-container"></div> \
  <div class="rule-operator-container"></div> \
  <div class="rule-value-container"></div> \
</div>',
        },
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
