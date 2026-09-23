define([
    'ko',
    'jquery',
    'uiComponent'
], function (ko, $, Component) {
    'use strict';
    var mixin = {
        /**
         * @param {Boolean} isHidden
         */
        onHiddenChange: function (isHidden) {

            if (isHidden) {
                setTimeout(function () {
                    $(this.selector).hide('blind', {}, this.hideSpeed);
                }.bind(this), 45000);
            }
        }
    };
    return function (target) {
        return target.extend(mixin);
    };
});
