/*jslint nomen:true*/
/*global define*/
define([
    'jquery',
    'underscore',
    'oroui/js/mediator',
    './abstract-grid-change-listener'
], function ($, _, mediator, AbstractGridChangeListener) {
    'use strict';

    var ColumnFormListener;

    /**
     * Listener for entity edit form and datagrid
     *
     * @export  orodatagrid/js/datagrid/listener/column-form-listener
     * @class   orodatagrid.datagrid.listener.ColumnFormListener
     * @extends orodatagrid.datagrid.listener.AbstractGridChangeListener
     */
    ColumnFormListener = AbstractGridChangeListener.extend({

        /** @param {Object} */
        selectors: {
            included: null,
            excluded: null
        },

        /**
         * @inheritDoc
         */
        initialize: function (options) {
            if (!_.has(options, 'selectors')) {
                throw new Error('Field selectors is not specified');
            }
            this.selectors = options.selectors;
            this.confirmModal = {};

            ColumnFormListener.__super__.initialize.apply(this, arguments);
        },

        /**
         * @inheritDoc
         */
        setDatagridAndSubscribe: function () {
            ColumnFormListener.__super__.setDatagridAndSubscribe.apply(this, arguments);

            /** Restore include/exclude state from pagestate */
            mediator.bind("pagestate_restored", function () {
                this._restoreState();
            }, this);
        },

        /**
         * @inheritDoc
         */
        _processValue: function (id, model) {
            var original = this.get('original');
            var included = this.get('included');
            var excluded = this.get('excluded');

            var isActive = model.get(this.columnName);
            var originallyActive;
            if (_.has(original, id)) {
                originallyActive = original[id];
            } else {
                originallyActive = !isActive;
                original[id] = originallyActive;
            }

            if (isActive) {
                if (originallyActive) {
                    included = _.without(included, [id]);
                } else {
                    included = _.union(included, [id]);
                }
                excluded = _.without(excluded, id);
            } else {
                included = _.without(included, id);
                if (!originallyActive) {
                    excluded = _.without(excluded, [id]);
                } else {
                    excluded = _.union(excluded, [id]);
                }
            }

            this.set('included', included);
            this.set('excluded', excluded);
            this.set('original', original);

            this._synchronizeState();
        },

        /**
         * @inheritDoc
         */
        _clearState: function () {
            this.set('included', []);
            this.set('excluded', []);
            this.set('original', {});
        },

        /**
         * @inheritDoc
         */
        _synchronizeState: function () {
            var included = this.get('included');
            var excluded = this.get('excluded');
            if (this.selectors.included) {
                $(this.selectors.included).val(included.join(','));
            }
            if (this.selectors.excluded) {
                $(this.selectors.excluded).val(excluded.join(','));
            }
            mediator.trigger('datagrid:setParam:' + this.gridName, 'data_in', included);
            mediator.trigger('datagrid:setParam:' + this.gridName, 'data_not_in', excluded);
        },

        /**
         * Explode string into int array
         *
         * @param string
         * @return {Array}
         * @private
         */
        _explode: function (string) {
            if (!string) {
                return [];
            }
            return _.map(string.split(','), function (val) { return val ? parseInt(val, 10) : null; });
        },

        /**
          * Restore values of include and exclude properties
          *
          * @private
          */
        _restoreState: function () {
            var included = '';
            var excluded = '';
            if (this.selectors.included && $(this.selectors.included).length) {
                included = this._explode($(this.selectors.included).val());
                this.set('included', included);
            }
            if (this.selectors.excluded && $(this.selectors.excluded).length) {
                excluded = this._explode($(this.selectors.excluded).val());
                this.set('excluded', excluded);
            }
            if (included || excluded) {
                mediator.trigger('datagrid:setParam:' + this.gridName, 'data_in', included);
                mediator.trigger('datagrid:setParam:' + this.gridName, 'data_not_in', excluded);
                mediator.trigger('datagrid:restoreState:' + this.gridName, this.columnName, this.dataField, included, excluded);
            }
        },

        /**
         * @inheritDoc
         */
        _hasChanges: function () {
            return !_.isEmpty(this.get('included')) || !_.isEmpty(this.get('excluded'));
        }
    });

    /**
     * Builder interface implementation
     *
     * @param {jQuery.Deferred} deferred
     * @param {Object} options
     * @param {jQuery} [options.$el] container for the grid
     * @param {string} [options.gridName] grid name
     * @param {Object} [options.gridPromise] grid builder's promise
     * @param {Object} [options.data] data for grid's collection
     * @param {Object} [options.metadata] configuration for the grid
     */
    ColumnFormListener.init = function (deferred, options) {
        var gridOptions, gridInitialization;
        gridOptions = options.metadata.options || {};
        gridInitialization = options.gridPromise;

        var gridListenerOptions = gridOptions.rowSelection || gridOptions.columnListener; // for BC

        if (gridListenerOptions) {
            gridInitialization.done(function (grid) {
                var listener, listenerOptions;
                listenerOptions = _.defaults({
                    $gridContainer: grid.$el,
                    gridName: grid.name
                }, gridListenerOptions);

                listener = new ColumnFormListener(listenerOptions);
                deferred.resolve(listener);
            }).fail(function () {
                deferred.reject();
            });
        } else {
            deferred.reject();
        }
    };

    return ColumnFormListener;
});
