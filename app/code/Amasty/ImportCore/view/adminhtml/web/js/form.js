define([
    'Magento_Ui/js/form/form',
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
], function (Form, $, _, registry, confirm, $t) {
    'use strict';

    return Form.extend({
        defaults: {
            downloadSampleFileConfig: {
                confirmMsg: $t('All possible fields will be included in the sample file without configured mapping. Do you really want to proceed?'),
                mapFieldsPath: 'data.fields.fields',
                scrollToFieldsConfigElm: '',
                openFieldsConfigTab: ''
            },
            listens: {
                'responseData': 'processResponse'
            },
            modules: {
                run: 'index = run'
            }
        },
        _origSaveUrl: false,

        initialize: function () {
            this._super();

            this._origSaveUrl = this.source.client.urls.save;

            return this;
        },

        save: function (redirect, data) {
            this._setSaveUrl();

            this._super(redirect, data);
        },

        processResponse: function () {
            var responseData = this.responseData();

            if (!_.isUndefined(responseData.download) && responseData.download) {
                this._downloadFile(responseData.filename, responseData.content);
            }
        },

        downloadSampleFile: function (params) {
            let self = this,
                fieldsConfigInstance;

            this.validate();
            if (!this.source.get('params.invalid')) {
                if (this._isMappedFields()) {
                    self._changeUrlAndSubmit(params.url);
                } else {
                    confirm({
                        content: self.downloadSampleFileConfig.confirmMsg,
                        actions: {
                            confirm: function () {
                                self._changeUrlAndSubmit(params.url);
                            }
                        },
                        buttons: [
                            {
                                text: $t('Yes'),
                                class: 'action-secondary action-dismiss',
                                click: function (event) {
                                    this.closeModal(event, true);
                                }
                            }, {
                                text: $t('Configure Mapping First'),
                                class: 'action-primary action-accept',
                                click: function (event) {
                                    this.closeModal(event);
                                    if (self.downloadSampleFileConfig.openFieldsConfigTab) {
                                        fieldsConfigInstance = registry.get(
                                            self.downloadSampleFileConfig.openFieldsConfigTab
                                        );

                                        if (fieldsConfigInstance) {
                                            fieldsConfigInstance.activate();
                                        }
                                    }

                                    if (self.downloadSampleFileConfig.scrollToFieldsConfigElm) {
                                        fieldsConfigInstance = $(self.downloadSampleFileConfig.scrollToFieldsConfigElm);

                                        $([document.documentElement, document.body]).animate({
                                            scrollTop: fieldsConfigInstance.offset().top - 77
                                        }, 2000);
                                    }
                                }
                            }
                        ]
                    });
                }
            } else {
                this.focusInvalid();
            }
        },

        _isMappedFields: function () {
            let result = false,
                entities;

            entities = Object.values(this.source.get(this.downloadSampleFileConfig.mapFieldsPath))[0];

            entities['parent'] = {'fields': entities.fields};
            _.each(entities, function (subEntity) {
                if (_.isObject(subEntity) && 'fields' in subEntity
                    && _.isObject(subEntity.fields) && Object.keys(subEntity.fields).length
                ) {
                    result = true;
                }
            }.bind(this));

            return result;
        },

        _setSaveUrl: function () {
            this.source.client.urls.save = this._origSaveUrl;
        },

        _changeUrlAndSubmit: function (url) {
            this.source.client.urls.save = url;
            this.submit();
        },

        _downloadFile: function (filename, content) {
            let a = document.createElement('a');

            document.body.appendChild(a);
            a.style = 'display: none';
            a.download = filename;
            a.href = content;
            a.click();
            window.URL.revokeObjectURL(content);
        }
    });
});
