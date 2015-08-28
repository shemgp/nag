(function($) {
    FormValidation.Validator.in = {
        /**
         * @param {FormValidation.Base} validator The validator plugin instance
         * @param {jQuery} $field The jQuery object represents the field element
         * @param {Object} options The validator options
         * @returns {Boolean}
         */
        validate: function(validator, $field, options) {

            var values = (typeof options == 'string') ? options : $field.data('vf-in-values');
            var requiredValues = values.split(',');

            return $.inArray($field.val(), requiredValues) >= 0;
        }
    };
    FormValidation.Validator.notin = {
        /**
         * @param {FormValidation.Base} validator The validator plugin instance
         * @param {jQuery} $field The jQuery object represents the field element
         * @param {Object} options The validator options
         * @returns {Boolean}
         */
        validate: function(validator, $field, options) {

            var values = (typeof options == 'string') ? options : $field.data('vf-notin-values');
            var requiredValues = values.split(',');

            return $.inArray($field.val(), requiredValues) < 0;
        }
    };

    FormValidation.Validator.activeurl = {
        /**
         * @param {FormValidation.Base} validator The validator plugin instance
         * @param {jQuery} $field The jQuery object represents the field element
         * @param {Object} options The validator options
         * @returns {Boolean}
         */
        validate: function(validator, $field, options) {

            var promise = new $.Deferred();

            $.get($field.val(), function(){
                promise.resolve({
                   valid: true
                });
            }).fail(function(){
                promise.reject({
                    valid: false
                });
            });

            return promise.promise();
        }
    };
}(window.jQuery));