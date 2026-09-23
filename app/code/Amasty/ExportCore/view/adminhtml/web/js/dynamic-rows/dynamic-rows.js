define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (DynamicRows) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            parentComponent: null,
            listens: {
                relatedData: 'checkRelatedData'
            },
            templates: {
                record: {
                    parent: '${ $.$data.collection.name }',
                    name: '${ $.$data.index }',
                    dataScope: 'filters.${ $.name }',
                    nodeTemplate: '${ $.parent }.${ $.$data.collection.recordTemplate }'
                },
            },
            links: {
                recordData: '${ $.provider }:${ $.dataScope }.filters'
            },
        },

        checkRelatedData: function () {
            if (this.parentComponent && this.relatedData.length) {
                this.parentComponent.opened(true);
            }
        },

        initContainer: function (parent) {
            this._super();

            this.parentComponent = parent;

            if (this.relatedData.length) {
                this.parentComponent.opened(true);
            }

            return this;
        },

        /**
         * Init header elements
         * @return void
         */
        initHeader: function () {
            var labels = [],
                data;

            if (!this.labels().length) {
                _.each(this.childTemplate.children, function (cell) {
                    data = this.createHeaderTemplate(cell.config);
                    cell.config.labelVisible = false;
                    _.extend(data, {
                        defaultLabelVisible: data.visible(),
                        label: cell.config.label,
                        name: cell.name,
                        required: !!cell.config.validation,
                        columnsHeaderClasses: cell.config.columnsHeaderClasses,
                        sortOrder: cell.config.sortOrder,
                        labelTooltip: cell.config.labelTooltip
                    });
                    labels.push(data);
                }, this);
                this.labels(_.sortBy(labels, 'sortOrder'));
            }
        },
    });
});
