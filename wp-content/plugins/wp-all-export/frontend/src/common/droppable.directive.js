GoogleMerchants.directive('droppable', [function() {
    return {
        restrict: 'A',
        require: '^ngModel',
        link: function (scope, element, attributes, ngModelCtrl) {

            function placeCaretAtEnd(el) {
                el.focus();
                if (typeof window.getSelection != "undefined"
                    && typeof document.createRange != "undefined") {
                    var range = document.createRange();
                    range.selectNodeContents(el);
                    range.collapse(false);
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                } else if (typeof document.body.createTextRange != "undefined") {
                    var textRange = document.body.createTextRange();
                    textRange.moveToElementText(el);
                    textRange.collapse(false);
                    textRange.select();
                }
            }


            function processElementName($element, elementName){

                if ( $element.find('input[name^=cc_type]').val().indexOf('image_') !== -1 )
                {
                    elementName = 'Image ' + elementName;
                }
                if ( $element.find('input[name^=cc_type]').val().indexOf('attachment_') !== -1 )
                {
                    elementName = 'Attachment ' + elementName;
                }
                return elementName;
            }

            var $element;

            if(element[[0]].nodeName == 'STYLED-INPUT') {
                $element = angular.element(element).find('div');

                $element.droppable({
                    over: function(event, ui) {
                        jQuery('body').css('cursor','copy');
                    },
                    drop: function( event, ui ) {

                        var $droppedElement = ui.draggable.find('.custom_column');
                        var elementName = $droppedElement.find('input[name^=cc_name]').val();
                        elementName = processElementName($droppedElement, elementName);
                        $element.html($element.html() + '<strong>{' + elementName + '}</strong>&#8203;');
                        placeCaretAtEnd($element[0]);
                        $element.focus();
                        //ngModelCtrl.$setViewValue($element.val());
                        //ngModelCtrl.$render();
                    }
                });
            } else {
                $element = angular.element(element);

                $element.droppable({
                    over: function(event, ui) {
                        jQuery(this).css("cursor", "copy");
                    },
                    out: function(event, ui) {
                        jQuery(this).css("cursor", "initial");
                    },
                    drop: function( event, ui ) {
                        jQuery(this).css("cursor", "initial");
                        var $droppedElement = ui.draggable.find('.custom_column');
                        var elementName = $droppedElement.find('input[name^=cc_name]').val();
                        elementName = processElementName($droppedElement, elementName);
                        $element.val($element.val() + '{' + elementName + '}');
                        ngModelCtrl.$setViewValue($element.val());
                        ngModelCtrl.$render();
                    }
                });
            }


        }
    };
}]);