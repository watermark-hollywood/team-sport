'use strict';

var confs = {
    storage: {
        /**
         * Get Email from localStorage
         */
        get: function() {
            return new Promise(function(resolve, reject) {
                try {
                    var email = JSON.parse(localStorage.getItem('jqueryEmail')) || {
                            // name: 'New Email',
                            elements: [],
                            html: '',
                            emailSettings: {
                                options: {
                                    paddingTop: "50px",
                                    paddingLeft: "5px",
                                    paddingBottom: "50px",
                                    paddingRight: "5px",
                                    backgroundColor: "#cccccc"
                                },
                                type: 'emailSettings'
                            }
                        };
                    setTimeout(function() {
                        resolve(email)
                    }, 1000)
                } catch (e) {
                    utils.notify(e).error();
                    reject(e)
                }
            });
        },

        /**
         * Put changed data in Email
         * Emulate server storage with Promise
         * @param email
         * @returns {Promise}
         */
        put: function(email) {
            return new Promise(function(resolve, reject) {
                try {
                    // Remove multine breaks
                    email.html = utils.removeLineBreaks(email.html);
                    localStorage.setItem('jqueryEmail', JSON.stringify(email));
                    resolve()
                } catch (e) {
                    utils.notify(e).error();
                    reject(e)
                }
            })
        }
    },
    options: {
        urlToUploadImage: '//uploads.im/api',
        trackEvents: false // You need to add google analytics in index.html
    }
};
var utils = {
    /**
     * Convert string from snake to camel
     * @param str
     * @returns {*}
     */
    snakeToCamel: function(str) {
        if (typeof str !== 'string')  return str;
        return str.replace(/_([a-z])/gi, function(m, w) {
            return "" + w.toUpperCase();
        });
    },
    /**
     * Convert camel to snake
     * @param str
     * @returns {*}
     */
    camelToSnake: function(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/([A-Z])/g, function(m, w) {
            return "_" + w.toLowerCase();
        });
    },
    /**
     * Generate random id
     * @param prefix
     * @returns {string}
     */
    uid: function(prefix) {
        return (prefix || 'id') + (new Date().getTime()) + "RAND" + (Math.ceil(Math.random() * 100000));
    },
    /**
     * Strip email html for unnecessary attributes, classes ...
     * @param htmlToInsert
     * @param settings
     * @returns {string|*|Object|string|string}
     */
    stripTags: function(htmlToInsert, settings) {
        var builderDoc = document.createElement("html");
        $(builderDoc).append($('<head/>'));
        $(builderDoc).append($('<body/>'));

        // All meta and styles in head
        if (!$(builderDoc).find('head meta[http-equiv="Content-Type"]').length) {
            $(builderDoc).find('head').append($('<meta/>', {
                'http-equiv': 'Content-Type',
                'content': 'text/html; charset=UTF-8'
            }));
        }
        if (!$(builderDoc).find('head meta[name="viewport"]').length) {
            $(builderDoc).find('head').append($('<meta/>', {
                'name': 'viewport',
                'content': 'width=device-width',
                'initial-scale': '1.0',
                'user-scalable': 'yes'
            }));
        }
        if (!$(builderDoc).find('head style#builder-styles').length) {
            var builderStyles = $(document).find('style#builder-styles').clone();
            $(builderDoc).find('head').append(builderStyles);
        }

        // Body style and html
        $(builderDoc).find('body').css({
            'background': settings.options.backgroundColor,
            'padding': settings.options.paddingTop + ' ' + settings.options.paddingRight + ' ' + settings.options.paddingBottom + ' ' + settings.options.paddingLeft
        }).html(htmlToInsert);

        $(builderDoc).find('i.actions').each(function() {
            $(this).remove();
        });
        $(builderDoc).find('.builder-element').each(function() {
            $(this).replaceWith($(this).contents());
        });
        $(builderDoc).contents().contents().addBack().filter(function() {
            return this.nodeType == Node.COMMENT_NODE;
        }).remove();

        return $(builderDoc)[0].outerHTML;
    },

    /**
     * Notify
     * @param msg
     * @param callback
     * @returns {{log: log, success: success, error: error}}
     */
    notify: function(msg, callback) {
        return {
            log: function() {
                return alertify.log(msg, callback)
            },
            success: function() {
                alertify.success(msg, callback)
            },
            error: function() {
                alertify.error(msg, callback)
            }
        }
    },

    /**
     * Confirm dialog
     * @param msg
     * @param succesFn
     * @param cancelFn
     * @param okBtn
     * @param cancelBtn
     * @returns {IAlertify}
     */
    confirm: function(msg, succesFn, cancelFn, okBtn, cancelBtn) {
        return alertify
            .okBtn(okBtn)
            .cancelBtn(cancelBtn)
            .confirm(msg, succesFn, cancelFn)
    },

    /**
     * Alert dialog
     * @param msg
     * @returns {IAlertify}
     */
    alert: function(msg) {
        return alertify
            .okBtn("Accept")
            .alert(msg)
    },

    /**
     * Prompt dialog
     * @param defaultvalue
     * @param promptMessage
     * @param successFn
     * @param cancelFn
     * @returns {IAlertify}
     */
    prompt: function(defaultvalue, promptMessage, successFn, cancelFn) {
        return alertify
            .defaultValue(defaultvalue)
            .prompt(promptMessage, successFn, cancelFn)
    },

    /**
     * Validate email before save and import
     * @param emailToValidate
     * @returns {boolean}
     */
    validateEmail: function(emailToValidate) {
        return Vue.util.isObject(emailToValidate) &&
            $.isArray(emailToValidate.elements) &&
            typeof emailToValidate.html == 'string' &&
            Vue.util.isObject(emailToValidate.emailSettings) &&
            emailToValidate.emailSettings.type == 'emailSettings' &&
            Vue.util.isObject(emailToValidate.emailSettings.options)
    },

    /**
     * Track events with Google Analytics
     * @param category
     * @param event
     * @param name
     * @returns {*}
     */
    trackEvent: function(category, event, name) {
        if (confs.trackEvents) {
            if (!ga)
                throw new Error('To track events, include Google analytics code in index.html');
            return ga('send', 'event', category, event, name);
        }
    },
    equals: function(obj1, obj2) {
        function _equals(obj1, obj2) {
            var clone = $.extend(true, {}, obj1),
                cloneStr = JSON.stringify(clone);
            return cloneStr === JSON.stringify($.extend(true, clone, obj2));
        }
        return _equals(obj1, obj2) && _equals(obj2, obj1);
    },
    removeLineBreaks: function(html) {
        return html.replace(/\n\s*\n/gi, '\n');
    },
    initTooltips: function() {
        setTimeout(function() {
            $('i[title]').powerTip({
                placement: 'sw-alt' // north-east tooltip position
            });
        }, 100)
    }
};
new Vue({
    data: function() {
        return {
            loading: true
        }
    },
    components: {
        'email-builder-component': function(resolve, reject) {
            Promise.all([$.get('builder/builder.html'), confs.storage.get()]).then(function(data) {
                resolve({
                    data: function() {
                        return {
                            preview: false,
                            currentElement: {},
                            elements: [
                                {
                                    type: 'title',
                                    icon: '&#xE165;',
                                    primary_head: 'Title',
                                    second_head: 'And subtitle'
                                },
                                {
                                    type: 'divider',
                                    icon: '&#xE8E9;',
                                    primary_head: 'Divider',
                                    second_head: '1px separation line'
                                },
                                {
                                    type: 'text',
                                    icon: '&#xE8EE;',
                                    primary_head: 'Text',
                                    second_head: 'Editable text box'
                                },
                                {
                                    type: 'image',
                                    icon: '&#xE40B;',
                                    primary_head: 'Image',
                                    second_head: 'Image without text'
                                },
                                {
                                    type: 'button',
                                    icon: '&#xE913;',
                                    primary_head: 'Button',
                                    second_head: 'Clickable URL button"'
                                },
                                {
                                    type: 'imageTextInside',
                                    icon: '&#xE060;',
                                    primary_head: 'Image/Text',
                                    second_head: 'Image inside text'
                                },
                                {
                                    type: 'imageTextLeft',
                                    icon: '&#xE060;',
                                    primary_head: 'Image/Text',
                                    second_head: 'Text on the left'
                                },
                                {
                                    type: 'imageTextRight',
                                    icon: '&#xE060;',
                                    primary_head: 'Image/Text',
                                    second_head: 'Text on the right'
                                },
                                {
                                    type: 'imageText2x2',
                                    icon: '&#xE060;',
                                    primary_head: 'Image/Text',
                                    second_head: '2 columns'
                                },
                                {
                                    type: 'imageText3x2',
                                    icon: '&#xE060;',
                                    primary_head: 'Image/Text',
                                    second_head: '3 columns'
                                },

                            ],
                            defaultOptions: {
                                'title': {
                                    type: 'title',
                                    options: {
                                        align: 'center',
                                        title: 'Enter your title here', // Enter your title here
                                        subTitle: 'Subtitle', // Subtitle
                                        padding: ["5px", "50px", "5px", "50px"],
                                        backgroundColor: '#ffffff',
                                        color: '#444444'
                                    }
                                },
                                'divider': {
                                    type: 'divider',
                                    options: {
                                        padding: ['15px', '50px', '0px', '50px'],
                                        backgroundColor: '#ffffff'
                                    }
                                },
                                'text': {
                                    type: 'text',
                                    options: {
                                        padding: ['10px', '50px', '10px', '50px'],
                                        backgroundColor: '#ffffff',
                                        text: '<p style="margin:0 0 10px 0;line-height:22px;font-size:13px;" data-block-id="text-area">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>'
                                    }
                                },
                                'button': {
                                    type: 'button',
                                    options: {
                                        align: 'center',
                                        padding: ['15px', '50px', '15px', '50px'],
                                        buttonText: 'Click me',
                                        url: '#',
                                        buttonBackgroundColor: '#3498DB',
                                        backgroundColor: '#ffffff'
                                    }
                                },
                                'image': {
                                    type: 'image',
                                    options: {
                                        align: 'center',
                                        padding: ["15px", "50px", "15px", "50px"],
                                        image: location.origin + '/portal/builder/assets/350x150.jpg',
                                        backgroundColor: '#ffffff'
                                    }
                                },
                                'imageTextInside': {
                                    type: 'imageTextInside',
                                    options: {
                                        padding: ["15px", "50px", "15px", "50px"],
                                        image: location.origin + '/portal/builder/assets/370x160.jpg',
                                        width: '370',
                                        backgroundColor: '#ffffff',
                                        text: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>'
                                    }
                                },
                                'imageTextRight': {
                                    type: 'imageTextRight',
                                    options: {
                                        padding: ["15px", "50px", "15px", "50px"],
                                        image: location.origin + '/portal/builder/assets/340x145.jpg',
                                        width: '340',
                                        backgroundColor: '#ffffff',
                                        text: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.</p>'
                                    }
                                },
                                'imageTextLeft': {
                                    type: 'imageTextLeft',
                                    options: {
                                        padding: ["15px", "50px", "15px", "50px"],
                                        image: location.origin + '/portal/builder/assets/340x145.jpg',
                                        width: '340',
                                        backgroundColor: '#ffffff',
                                        text: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.</p>'
                                    }
                                },
                                'imageText2x2': {
                                    type: 'imageText2x2',
                                    options: {
                                        padding: ["15px", "50px", "15px", "50px"],
                                        image1Hide: false,
                                        image1: location.origin + '/portal/builder/assets/255x154.jpg',
                                        image2Hide: false,
                                        image2: location.origin + '/portal/builder/assets/255x154.jpg',
                                        width1: '255',
                                        width2: '255',
                                        backgroundColor: '#ffffff',
                                        text1: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>',
                                        text2: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>'
                                    }
                                },
                                'imageText3x2': {
                                    type: 'imageText3x2',
                                    options: {
                                        padding: ["15px", "50px", "15px", "50px"],
                                        image1Hide: false,
                                        image1: location.origin + '/portal/builder/assets/154x160.jpg',
                                        image2Hide: false,
                                        image2: location.origin + '/portal/builder/assets/154x160.jpg',
                                        image3Hide: false,
                                        image3: location.origin + '/portal/builder/assets/154x160.jpg',
                                        width1: '154',
                                        width2: '154',
                                        width3: '154',
                                        backgroundColor: '#ffffff',
                                        text1: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>',
                                        text2: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>',
                                        text3: '<p style="line-height: 22px;">Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>'
                                    }
                                },

                            },
                            Email: data[1],
                            clonedEmail: JSON.parse(JSON.stringify(data[1]))
                        }
                    },
                    mounted: function() {
                        this.$root._data.loading = false;
                        utils.initTooltips();
                    },
                    watch: {
                        Email: {
                            handler: function() {
                                utils.initTooltips();
                            },
                            deep: true
                        }
                    },
                    computed: {
                        loading: function() {
                            return this.$root._data.loading;
                        }
                    },
                    methods: {
                        hasChanges: function() {
                            return !utils.equals(this.Email, this.clonedEmail);
                        },
                        editElement: function(id) {
                            if (!id) {
                                return this.currentElement = {};
                            }
                            var self = this,
                                editElement = id !== 'emailSettings' ? self.Email.elements.find(function(element) {
                                    return element.id == id;
                                }) : self.Email[id];

                            if (self.preview || self.currentElement == editElement) return;
                            self.currentElement = {};
                            setTimeout(function() {
                                self.currentElement = editElement;
                            }, 10);
                        },
                        removeElement: function(remElement) {
                            var self = this;
                            return utils.confirm('Are you sure?', function() {
                                self.Email.elements = self.Email.elements.filter(function(element) {
                                    return element != remElement;
                                });
                                if (utils.equals(self.currentElement, remElement)) {
                                    self.currentElement = {};
                                }
                            }, null, 'Delete element', 'Don\'t delete');

                        },
                        saveEmailTemplate: function() {
                            var self = this;
                            // Striping not necessary tags
                            this.Email.html = utils.stripTags($(self.$refs.emailElements.$el).html(), this.Email.emailSettings);
                            confs.storage.put(self.Email).then(function() {
                                utils.notify('Email has been saved.').success();
                                self.clonedEmail = JSON.parse(JSON.stringify(self.Email));
                                self.currentElement = {};
                            });
                        },
                        previewEmail: function() {
                            if (!this.Email.elements.length)
                                return utils.notify('Nothing to preview, please add some elements.').log();
                            this.preview = true;
                            this.currentElement = {};
                        },
                        exportEmail: function() {
                            if (!this.Email.elements.length)
                                return utils.notify('Nothing to export, please add some elements.').log();

                            var a = document.createElement('a');
                            a.href = 'data:attachment/html,' + encodeURI(this.Email.html);
                            a.target = '_blank';
                            a.download = utils.uid('export') + '.html';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        },
                        cloneElement: function(element) {
                            var newEl = JSON.parse(JSON.stringify(element));
                            newEl.id = utils.uid();
                            this.Email.elements.splice(this.Email.elements.indexOf(element) + 1, 0, newEl);
                        },
                        clone: function(obj) {
                            var newElement = $.extend(true, {}, this.defaultOptions[obj.type]);
                            newElement.id = utils.uid();
                            newElement.component = obj.type + 'Template';
                            return newElement;
                        },
                        importJson: function() {
                            var self = this;
                            var file = $('<input />', {
                                type: 'file',
                                name: 'import-file'
                            }).on('change', function() {
                                var importedFile = new FileReader();
                                importedFile.onload = function() {
                                    var importedData = JSON.parse(importedFile.result);
                                    if (utils.validateEmail(importedData)) {
                                        confs.storage.put(importedData).then(function() {
                                            self.currentElement = {};
                                            self.Email = $.extend({}, importedData);
                                            self.clonedEmail = $.extend({}, importedData);
                                            utils.notify('Email has been imported').success()
                                        });
                                    } else {
                                        utils.notify('Imported data isn\'t valid.').error()
                                    }
                                };
                                var fileToImport = this.files[0];
                                if (fileToImport.type !== 'application/json') {
                                    return utils.notify('Invalid format file').log()
                                }
                                importedFile.readAsText(fileToImport)
                            });

                            if (!self.Email.elements.length)
                                return file.click();

                            return utils.confirm('On import all current details will be deleted!', function() {
                                return file.click()
                            }, function() {
                                return utils.notify('Import canceled').log()
                            }, 'accept', 'deny')
                        },
                        exportJson: function() {
                            var a = document.createElement('a');
                            a.target = '_blank';
                            utils.trackEvent('Email', 'export', 'JSON');
                            a.href = 'data:attachment/json,' + encodeURI(JSON.stringify(this.Email));
                            a.download = utils.uid('export') + '.json';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        }
                    },
                    template: data[0],
                    directives: {
                        mdInput: {
                            bind: function(el, binding, vnode) {
                                var $elem = $(el);
                                var updateInput = function() {
                                    // clear wrapper classes
                                    $elem.closest('.md-input-wrapper').removeClass('md-input-wrapper-danger md-input-wrapper-success md-input-wrapper-disabled');

                                    if ($elem.hasClass('md-input-danger')) {
                                        $elem.closest('.md-input-wrapper').addClass('md-input-wrapper-danger')
                                    }
                                    if ($elem.hasClass('md-input-success')) {
                                        $elem.closest('.md-input-wrapper').addClass('md-input-wrapper-success')
                                    }
                                    if ($elem.prop('disabled')) {
                                        $elem.closest('.md-input-wrapper').addClass('md-input-wrapper-disabled')
                                    }
                                    if ($elem.hasClass('label-fixed')) {
                                        $elem.closest('.md-input-wrapper').addClass('md-input-filled')
                                    }
                                    if ($elem.val() != '') {
                                        $elem.closest('.md-input-wrapper').addClass('md-input-filled')
                                    }
                                };

                                setTimeout(function() {
                                    if (!$elem.hasClass('md-input-processed')) {

                                        if ($elem.prev('label').length) {
                                            $elem.prev('label').addBack().wrapAll('<div class="md-input-wrapper"/>');
                                        } else {
                                            $elem.wrap('<div class="md-input-wrapper"/>');
                                        }
                                        $elem
                                            .addClass('md-input-processed')
                                            .closest('.md-input-wrapper')
                                            .append('<span class="md-input-bar"/>');
                                    }

                                    updateInput();

                                }, 100);

                                $elem
                                    .on('focus', function() {
                                        $elem.closest('.md-input-wrapper').addClass('md-input-focus')
                                    })
                                    .on('blur', function() {
                                        setTimeout(function() {
                                            $elem.closest('.md-input-wrapper').removeClass('md-input-focus');
                                            if ($elem.val() == '') {
                                                $elem.closest('.md-input-wrapper').removeClass('md-input-filled')
                                            } else {
                                                $elem.closest('.md-input-wrapper').addClass('md-input-filled')
                                            }
                                        }, 100)
                                    });
                            }
                        },
                        inputFileUpload: {
                            twoWay: true,
                            bind: function(elem, binding, vnode) {
                                var wrapper, inputText;

                                setTimeout(function() {

                                    wrapper = $(elem).closest('.md-input-wrapper');
                                    inputText = wrapper.children('input:text');

                                    inputText.css('paddingRight', '35px');
                                    wrapper.append('<button type="button" class="md-icon upload-icon">\n    <i class="material-icons">file_upload</i>\n    <input type="file" name="file">\n</button>');

                                    wrapper.find('input[type=file]').bind('change', function (event) {

                                        if (!confs.options.urlToUploadImage)
                                            throw Error('You don\'t set the \'urlToUploadImage\' in variables.');

                                        var inputFile = $(this),
                                            icon = inputFile.prev('i.material-icons'),
                                            oldIconText = icon.text();
                                        icon.text('hdr_strong').addClass('icon-spin').css('opacity', '.7');
                                        inputFile.prop('disabled', true);
                                        var formData = new FormData();
                                        formData.append('upload', event.target.files[0]);
                                        return $.ajax({
                                            url: confs.options.urlToUploadImage,
                                            data: formData,
                                            processData: false,
                                            contentType: false,
                                            type: 'POST',
                                            success: function(res){
                                                if (res.status_code == 200) {
                                                    var customEvent = new Event('input', { bubbles: true }); // won't work in IE <11
                                                    $(elem).val(res.data.img_url);
                                                    elem.dispatchEvent(customEvent);
                                                    utils.notify('Your image has been uploaded').log()
                                                } else {
                                                    utils.notify(res.status_txt).error()
                                                }
                                            },
                                            error: function (err) {
                                                utils.notify(err.statusText).error()
                                            },
                                            complete: function () {
                                                inputFile.prop('disabled', false);
                                                icon.text(oldIconText).removeClass('icon-spin').removeAttr('style');
                                            }
                                        });
                                    })
                                }, 100);

                            },
                            unbind: function(elem) {
                                $(elem).unbind('change');
                            }
                        },
                        tinymceEditor: {
                            twoWay: true,
                            bind: function(elem) {
                                var self = elem;
                                tinymce.baseURL = 'bower_components/tinymce/';
                                setTimeout(function() {
                                    tinymce.init({
                                        target: self,
                                        inline: false,
                                        skin: 'lightgray',
                                        theme : 'modern',
                                        plugins: ["advlist autolink lists link image charmap", "searchreplace visualblocks code", "insertdatetime media table contextmenu paste", 'textcolor'],
                                        toolbar: "undo redo | styleselect | bold italic fontsizeselect forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                                        fontsize_formats: '8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 18pt 24pt 36pt',
                                        setup: function(editor) {
                                            // init tinymce
                                            editor.on('init', function() {
                                                editor.setContent(self.value);
                                            });
                                            // when typing keyup event
                                            editor.on('keyup change', function() {
                                                // get new value
                                                var customEvent = new Event('input', { bubbles: true }); // won't work in IE <11
                                                self.value = editor.getContent({format: 'raw'});
                                                elem.dispatchEvent(customEvent);
                                            });
                                        }
                                    });
                                }, 100)
                            },
                            unbind: function() {
                                tinymce.editors.forEach(function(editor) {
                                    return editor.destroy();
                                })
                            }
                        }
                    },
                    filters: {
                        makeTitle: function(value) {
                            if (!value) return '';
                            value = utils.camelToSnake(value);
                            value = value.charAt(0).toUpperCase() + value.slice(1);
                            return value.replace(/_/g, ' ');
                        }
                    },
                    components: {
                        titleTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" align="center" :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" data-type="title">\n    <tbody>\n    <tr>\n        <td :align="element.options.align" class="title" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" style="color: #757575;" data-block-id="background">\n            <h1 v-if="element.options.title.length" :style="{color: element.options.color}" style="font-family: Arial, sans-serif; margin: 0; font-weight: 800; line-height: 42px; font-size: 36px;" data-block-id="main-title">{{ element.options.title }}</h1>\n            <h4 v-if="element.options.subTitle.length" :style="{color: element.options.color}" style="font-family: Arial, sans-serif; font-weight: 500; margin-bottom: ; line-height: 22px; font-size: 16px;" data-block-id="sub-title">{{ element.options.subTitle }}</h4>\n        </td>\n    </tr>\n    </tbody>\n</table>'
                        },
                        buttonTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center" style="display: table;" :style="{backgroundColor: element.options.backgroundColor}" data-type="button">    <tbody>    <tr>        <td :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" class="buttons-full-width"><table cellspacing="0" cellpadding="0" border="0" :align="element.options.align" class="button"><tbody>    <tr>        <td style="margin: 10px 10px 10px 10px;" class="button">            <a :style="{backgroundColor: element.options.buttonBackgroundColor}" style="color: #FFFFFF;font-family: Arial,serif;font-size: 15px;line-height:21px;border-radius: 6px;text-align: center;text-decoration: none;font-weight: bold;display: block;margin: 0 0; padding: 12px 20px;" class="button-1" :href="element.options.url" data-default="1">{{ element.options.buttonText }}</a>                   <!--[if mso]>             </center>           </v:roundrect>         <![endif]-->        </td>    </tr>    </tbody></table>        </td>    </tr>    </tbody></table>'
                        },
                        textTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" align="center" data-type="text-block">    <tbody>    <tr>        <td class="block-text" data-block-id="background" align="left" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" style="font-size: 13px; color: #000000; line-height: 22px;" v-html="element.options.text">        </td>    </tr>    </tbody></table>'
                        },
                        socialTemplate: {
                            props: ['element'],
                            template: '<table class="main" align="center" width="640" cellspacing="0" cellpadding="0" border="0" :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" data-type="social-links">\n    <tbody>\n    <tr>\n        <td class="social" :align="element.options.align" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}">\n            <a :href="element.options.facebookLink" target="_blank" style="border: none;text-decoration: none;" class="facebook">\n                <img border="0" v-if="element.options.facebookLink.length" src="' + location.origin + '/assets/social/facebook.png">\n            </a>\n            <a :href="element.options.twitterLink" target="_blank" style="border: none;text-decoration: none;" class="twitter">\n                <img border="0" v-if="element.options.twitterLink.length" src="' + location.origin + '/assets/social/twitter.png">\n            </a>\n            <a :href="element.options.linkedinLink" target="_blank" style="border: none;text-decoration: none;" class="linkedin">\n                <img border="0" v-if="element.options.linkedinLink.length" src="' + location.origin + '/assets/social/linkedin.png">\n            </a>\n            <a :href="element.options.youtubeLink" target="_blank" style="border: none;text-decoration: none;" class="youtube">\n                <img border="0" v-if="element.options.youtubeLink.length" src="' + location.origin + '/assets/social/youtube.png">\n            </a>\n        </td>\n    </tr>\n    </tbody>\n</table>'
                        },
                        unsubscribeTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" align="center" data-type="text-block">    <tbody>    <tr>        <td data-block-id="background" align="left" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;"v-html="element.options.text">        </td>    </tr>    </tbody></table>'
                        },
                        dividerTemplate: {
                            props: ['element'],
                            template: '<table class="main" width="640" :style="{backgroundColor: element.options.backgroundColor}" style="border: 0; display: table;" cellspacing="0" cellpadding="0" border="0" align="center" data-type="divider">    <tbody>    <tr>        <td class="divider-simple" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid #DADFE1;">    <tbody>    <tr>        <td width="100%" height="15px"></td>    </tr>    </tbody></table>        </td>    </tr>    </tbody></table>'
                        },
                        imageTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main"  cellspacing="0" cellpadding="0" border="0" align="center" :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" data-type="image">    <tbody>    <tr>        <td :align="element.options.align" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" class="image"><img border="0" style="display:block;max-width:100%;" :src="element.options.image" tabindex="0">        </td>    </tr>    </tbody></table>'
                        },
                        imageTextInsideTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center"   :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" data-type="imageTextInside">    <tbody>    <tr>        <td align="left"   class="image-text"    :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}"     style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;"><table class="image-in-table" width="190" align="left" style="padding:5px 5px 5px 0; margin: 11px 0;">    <tbody>    <tr>        <td width="160">            <img border="0" align="left"                 :src="element.options.image"                 :width="element.options.width"                 style="display: block;margin: 0px;max-width: 540px;padding:0 10px 10px 0;">        </td>    </tr>    <tr>    </tr>    </tbody></table><div v-html="element.options.text"></div>        </td>    </tr>    </tbody></table>'
                        },
                        imageTextRightTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center" :style="{backgroundColor: element.options.backgroundColor}" style="display: table;" data-type="imageTextRight">    <tbody>    <tr>        <td class="image-text" align="left" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;"><table class="image-in-table" width="190" align="left" style="margin: 11px 0;">    <tbody>    <tr>        <td class="gap" width="30"></td>        <td width="160">            <img border="0" align="left" :src="element.options.image" :width="element.options.width" style="display: block;margin: 0px;max-width: 340px;padding:5px 5px 0 0;">        </td>    </tr>    </tbody></table><table width="190">    <tbody>    <tr>        <td class="text-block" v-html="element.options.text">        </td>    </tr>    </tbody></table>        </td>    </tr>    </tbody></table>'
                        },
                        imageTextLeftTemplate: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center"\n       :style="{backgroundColor: element.options.backgroundColor}"\n       style="display: table;" data-type="imageTextLeft">\n    <tbody>\n    <tr>\n        <td class="image-text" align="left"\n            :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}"\n            style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;">\n            <table width="190" align="left">\n                <tbody>\n                <tr>\n                    <td class="text-block" v-html="element.options.text"></td>\n                </tr>\n                </tbody>\n            </table>\n            <table class="image-in-table" width="190" align="right" style="margin: 11px 0;">\n                <tbody>\n                <tr>\n                    <td class="gap" width="30"></td>\n                    <td width="160">\n                        <img border="0" align="left"\n                             :src="element.options.image"\n                             :width="element.options.width"\n                             style="display: block;margin: 0px;max-width: 340px;padding:5px 5px 0 0;">\n                    </td>\n                </tr>\n                </tbody>\n            </table>\n        </td>\n    </tr>\n    </tbody>\n</table>'
                        },
                        imageText2x2Template: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center"\n       :style="{backgroundColor: element.options.backgroundColor}"\n       style="display: table;" data-type="imageText2x2Template">\n    <tbody>\n    <tr>\n        <td>\n            <table class="main" align="center" border="0" cellpadding="0" cellspacing="0"\n                   width="640" style="display: table;">\n                <tbody>\n                <tr>\n                    <td class="image-caption" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}" data-block-id="background">\n                        <table class="image-caption-column" align="left" border="0" cellpadding="0" cellspacing="0" width="255">\n                            <tbody>\n                            <tr v-if="!element.options.image1Hide">\n                                <td class="image-caption-content">\n                                    <img :src="element.options.image1"\n                                         :width="element.options.width1"\n                                         style="display: block;"\n                                         align="2" border="0">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-content text" align="left" style="font-family: Arial,serif;font-size: 13px;color: #000000;line-height: 22px;" v-html="element.options.text1">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-bottom-gap" height="5" width="100%"></td>\n                            </tr>\n                            </tbody>\n                        </table>\n                        <table class="image-caption-column" align="right" border="0" cellpadding="0"\n                               cellspacing="0" width="255">\n                            <tbody>\n                            <tr v-if="!element.options.image2Hide">\n                                <td class="image-caption-content">\n                                    <img :src="element.options.image2"\n                                         :width="element.options.width2"\n                                         style="display: block;"\n                                         align="2" border="0">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-content text" align="left" style="font-family: Arial,serif;font-size: 13px;color: #000000;line-height: 22px;" v-html="element.options.text2">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td height="5" width="100%"></td>\n                            </tr>\n                            </tbody>\n                        </table>\n                    </td>\n                </tr>\n                </tbody>\n            </table>\n        </td>\n    </tr>\n    </tbody>\n</table>'
                        },
                        imageText3x2Template: {
                            props: ['element'],
                            template: '<table width="640" class="main" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center"\n       :style="{backgroundColor: element.options.backgroundColor}"\n       style="display: table;" data-type="imageText3x2">\n    <tbody>\n    <tr>\n        <td class="image-caption" :style="{paddingTop: this.element.options.padding[0], paddingRight: this.element.options.padding[1], paddingBottom: this.element.options.padding[2], paddingLeft: this.element.options.padding[3]}">\n            <table class="image-caption-container" align="left" border="0" cellpadding="0" cellspacing="0" width="350">\n                <tbody>\n                <tr>\n                    <td>\n                        <table class="image-caption-column" align="left" border="0" cellpadding="0" cellspacing="0" width="160">\n                            <tbody>\n                            <tr>\n                                <td height="15" width="100%"></td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-content"\n                                    style="font-family: Arial,serif; font-size: 13px; color: #000000;">\n                                    <img :src="element.options.image1"\n                                         :width="element.options.width1"\n                                         style="display: block;" align="2" border="0">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td height="15" width="100%"></td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-content text"\n                                    style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;"\n                                    align="left"\n                                    v-html="element.options.text1">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-bottom-gap" height="5" width="100%"></td>\n                            </tr>\n                            </tbody>\n                        </table>\n                        <table class="image-caption-column" align="right" border="0" cellpadding="0" cellspacing="0" width="160">\n                            <tbody>\n                            <tr>\n                                <td class="image-caption-top-gap" height="15" width="100%"></td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-content"\n                                    style="font-family: Arial,serif; font-size: 13px; color: #000000;">\n                                    <img :src="element.options.image2"\n                                         :width="element.options.width2"\n                                         style="display: block;" align="2" border="0">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td height="15" width="100%"></td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-content text"\n                                    style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;"\n                                    align="left"\n                                    v-html="element.options.text2">\n                                </td>\n                            </tr>\n                            <tr>\n                                <td class="image-caption-bottom-gap" height="5" width="100%"></td>\n                            </tr>\n                            </tbody>\n                        </table>\n                    </td>\n                </tr>\n                </tbody>\n            </table>\n            <table class="image-caption-column" align="right" border="0" cellpadding="0" cellspacing="0"\n                   width="160">\n                <tbody>\n                <tr>\n                    <td class="image-caption-top-gap" height="15" width="100%"></td>\n                </tr>\n                <tr>\n                    <td class="image-caption-content"\n                        style="font-family: Arial,serif; font-size: 13px; color: #000000;">\n                        <img :src="element.options.image3"\n                             :width="element.options.width3"\n                             style="display: block;" align="2" border="0">\n                    </td>\n                </tr>\n                <tr>\n                    <td height="15" width="100%"></td>\n                </tr>\n                <tr>\n                    <td class="image-caption-content text"\n                        style="font-family: Arial,serif; font-size: 13px; color: #000000; line-height: 22px;"\n                        align="left"\n                        v-html="element.options.text3">\n                    </td>\n                </tr>\n                <tr>\n                    <td height="5" width="100%"></td>\n                </tr>\n                </tbody>\n            </table>\n        </td>\n    </tr>\n    </tbody>\n</table>'
                        }
                    }
                })
            }, reject)
        },
        'loading': {
            template: '<transition name="fade"><h1 class="loading" v-if="loading">Loading ...</h1></transition>',
            computed: {
                loading: function() {
                    return this.$root._data.loading;
                }
            }
        }
    }
}).$mount('#app');

// Prevent jQuery UI dialog from blocking focus in
$(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window, .moxman-window").length) {
        e.stopImmediatePropagation();
    }
});