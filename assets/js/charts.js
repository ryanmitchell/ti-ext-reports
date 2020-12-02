/*
 * tc Reports Charts plugin
 *
 * Data attributes:
 * - data-control="thoughtco-reports-chart" - enables the plugin on an element
 */
+function ($) {
    "use strict"

    // FIELD CHART CONTROL CLASS DEFINITION
    // ============================

    var tcReportsChart = function (element, options) {
        this.options = options
        this.$el = $(element)
        this.chartJs = null

        // Init
        this.initChartJs();
    }

    tcReportsChart.DEFAULTS = {
        alias: undefined,
        responsive: true,
        type: 'pie',
        options: {
            legend: {
                display: false,
            },
            maintainAspectRatio: true,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return data.labels[tooltipItem.index];        
                    },
                },
            },
        }
    }

    tcReportsChart.prototype.initChartJs = function () {
        this.options.data = JSON.parse(this.$el.find('textarea')[0].value);
        this.chartJs = new Chart(this.$el.find('canvas'), this.options)
        this.chartJs.resize()
    }

    tcReportsChart.prototype.unbind = function () {
        this.$el.tcReportsChart('destroy')
        this.$el.removeData('ti.tcReportsChart')
        this.chartJs = null
    }

    // FIELD CHART CONTROL PLUGIN DEFINITION
    // ============================

    var old = $.fn.tcReportsChart

    $.fn.tcReportsChart = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this = $(this)
            var data = $this.data('ti.tcReportsChart')
            var options = $.extend({}, tcReportsChart.DEFAULTS, $this.data(), typeof option === 'object' && option)
            if (!data) $this.data('ti.tcReportsChart', (data = new tcReportsChart(this, options)))
            if (typeof option === 'string') result = data[option].apply(data, args)
            if (typeof result !== 'undefined') return false
        })

        return result ? result : this
    }

    $.fn.tcReportsChart.Constructor = tcReportsChart

    // FIELD CHART CONTROL NO CONFLICT
    // =================

    $.fn.tcReportsChart.noConflict = function () {
        $.fn.tcReportsChart = old
        return this
    }

    // FIELD CHART CONTROL DATA-API
    // ===============

    $(document).render(function () {
        $('[data-control="thoughtco-reports-chart"]').tcReportsChart()
    })
    
}(window.jQuery)