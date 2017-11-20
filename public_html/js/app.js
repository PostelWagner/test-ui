/**
 * Created by Сергей on 18.11.2017.
 */

(function($) {

    var emailsData = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '/email/autocomplete',
            cache: false,
            prepare: function (query, settings) {
                settings.type = "GET";
                settings.contentType = "application/json; charset=UTF-8";
                settings.data = {
                    query: query,
                }
                return settings;
            },
            wildcard: '%QUERY%'
        },
    });

    function EmailRaw($modalBody, id, firstrow){
        var self = {
            $default_link: 'TDS link',
            $row:null,
            $link_field:null,
            $email_field:null,
            $errors_block:null,
            $email_block:null,
            $link_block:null,
            $btn_block:null,
            $pencil_btn:null,
            $erase_btn:null,
            $right_btn:null,
            $validLink:true,
            $validEmail:true,
            $hasLink:false,
            $id:null,

            get isValid() {
                self.validate();
                return self.$validLink && self.$validEmail;
            },

            init: function ($modalBody, id, firstrow = false) {
                self.$id = id;
                self.create($modalBody, firstrow);
            },
            create: function (container, top = false) {
                self.$row =  $('<div>')
                    .addClass('row')
                    .appendTo(container);

                self.$email_block =  $('<div>')
                    .addClass('col-md-5')
                    .appendTo(self.$row);
                self.$link_block =  $('<div>')
                    .addClass('col-md-6')
                    .appendTo(self.$row);
                self.$btn_block =  $('<div>')
                    .addClass('col-md-1')
                    .appendTo(self.$row);

                self.$errors_block = $('<div>')
                    .addClass('col-md-12 param-errors')
                    .appendTo(self.$row);

                self.$email_field =  $('<input type="text">')
                    .addClass('form-control')
                    .attr('name', 'list[' + self.$id + '][email]')
                    .data('type', 'email')
                    .appendTo(self.$email_block)
                    .blur(self.validateEmail)
                    .change(self.validateEmail)
                    .typeahead({
                        minLength: 0,
                        highlight: true
                    }, {
                        name: 'email',
                        limit: 10,
                        display: 'email',
                        source: emailsData,
                        templates: {
                            suggestion: function(data) {
                                return '<p><strong>' + data.email + '</strong>' +
                                    (data.link?(' - ' + data.link.substr(0, 100) + ( data.link.length > 100 ? '...' : '')) : '') +
                                    '</p>';
                            }
                        }
                    }).on('typeahead:selected', function(evt, item) {
                        if (item.link)
                            self.onSearchLinkSuccess(item.link);
                    });

                var group =  $('<div>')
                    .addClass('input-group')
                    .appendTo(self.$link_block);

                var group_btn =  $('<div>')
                    .addClass('input-group-btn')
                    .appendTo(group);

                self.$pencil_btn = $('<button>')
                    .addClass('btn btn-default')
                    .data('action', 'editLink')
                    .attr('type', 'button')
                    .html('<span class="glyphicon glyphicon-pencil"></span>')
                    .click(self.onEditLink)
                    .appendTo(group_btn);

                self.$erase_btn = $('<button>')
                    .addClass('btn btn-default disabled')
                    .data('action', 'eraseLink')
                    .attr('type', 'button')
                    .prop('disabled', true)
                    .html('<span class="glyphicon glyphicon-erase"></span>')
                    .click(self.onEraseLink)
                    .appendTo(group_btn);

                self.$link_field =  $('<input type="text">')
                    .addClass('form-control')
                    .data('type', 'link')
                    .attr('name', 'list[' + self.$id + '][link]')
                    .prop('disabled', true)
                    .val(self.$default_link)
                    .appendTo(group)
                    .blur(self.validateLink)
                    .change(self.validateLink);

                if (top) {
                    self.$right_btn = $('<button>')
                        .addClass('btn btn-default')
                        .data('action', 'add')
                        .attr('type', 'button')
                        .html('<span class="glyphicon glyphicon-plus"></span>')
                        .appendTo(self.$btn_block);
                } else {
                    self.$right_btn = $('<button>')
                        .addClass('btn btn-default')
                        .data('action', 'remove')
                        .attr('type', 'button')
                        .html('<span class="glyphicon glyphicon-minus"></span>')
                        .click(self.onRemove)
                        .appendTo(self.$btn_block);
                }

            },
            onRemove: function () {
                self.$row.remove();
            },
            onEditLink: function () {
                self.switchLinkBlock();
                self.onSearchLinkSuccess();
            },
            onSearchLink: function () {
                self.request('/email/link', {email: self.$email_field.val()}, self.onSearchLinkSuccess);
            },
            onSearchLinkSuccess: function (link) {
                if (!link) {
                    link = self.$link_field.data('link');
                } else {
                    self.$link_field.data('link', link);
                }
                if (self.$hasLink) {
                    self.$link_field.val(link);
                }
            },
            onEraseLink: function () {
                self.request('/email/erase-link', {email: self.$email_field.val()}, self.onEraseLinkSuccess);
            },
            onEraseLinkSuccess: function (response) {
                self.$link_field.val('');
            },
            switchLinkBlock: function (forceeDisable = false) {
                if (self.$hasLink || forceeDisable) {
                    self.$hasLink = false;
                    self.$erase_btn.addClass('disabled');
                    self.$erase_btn.prop('disabled', true);
                    self.$link_field.prop('disabled', true).val(self.$default_link);
                    self.$pencil_btn.removeClass('active');
                    self.validateLink();
                } else {
                    self.$hasLink = true;
                    self.$erase_btn.removeClass('disabled');
                    self.$erase_btn.prop("disabled", false);
                    self.$link_field.prop("disabled", false);
                    self.$pencil_btn.addClass('active');
                }
            },
            showErrors: function () {
                self.$email_block.removeClass('has-error');
                self.$link_block.removeClass('has-error');
                self.$errors_block.text('');
                if (!self.$validEmail) {
                    self.$email_block.addClass('has-error');
                    self.$errors_block.append('email format error ')
                }
                if (!self.$validLink) {
                    self.$link_block.addClass('has-error');
                    self.$errors_block.append('link format error')
                }
            },
            validate: function () {
                self.validateEmail();
                self.validateLink();
            },
            validateEmail: function () {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                self.$validEmail = regex.test(self.$email_field.val());
                self.showErrors();
            },
            validateLink: function () {
                if (!self.$hasLink) {
                    self.$validLink = true;
                } else {
                    var regex = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i;
                    self.$validLink = regex.test(self.$link_field.val());
                }
                self.showErrors();
            },
            request: function (url, data, callback) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                }).done(function(response){
                    callback(response);
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(url + jqXHR.status );
                })
            },
        }
        self.init($modalBody, id, firstrow);
        return self;
    };

    function EmailList($modal) {
        var self = {
            $modal_body: null,
            $template:null,
            $firstRaw:null,
            $current_id:1,
            $list: [],

            get template_id() {
                return self.$template.val();
            },

            set template_id(value) {
                self.$template.val(value);
            },

            init: function ($modal) {
                self.$modal_body = $('.modal-body', $modal);
                self.$template =  $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'template')
                    .appendTo('.modal-body', $modal);
            },
            addFirst: function(){
                var raw = new EmailRaw(self.$modal_body, self.$current_id, true);
                raw.$right_btn.on('click', self.add);
                self.$list.push(raw);
            },
            add: function () {
                var raw = new EmailRaw(self.$modal_body, ++self.$current_id);
                self.$list.push(raw);
            },
            updateList: function() {
                self.$list = self.$list.filter(function( row ) {
                    return row.$row.parent().length == 1;
                });
            },
            validate: function () {
                var current_email, emails = [], isValid = true;
                self.updateList();
                self.$list.forEach(function(row, i, arr) {
                    current_email = row.$email_field.val();
                    if (emails.indexOf(current_email) != -1) {
                        row.$email_block.addClass('has-error');
                        row.$errors_block.append('Email already set');
                        isValid = false;
                    } else {
                        isValid &= row.isValid;
                    }
                    emails.push(current_email);
                });
                return isValid;
            },
            clear: function() {
                //self.$firstRaw.clear();
                while (self.$list.length) {
                    row = self.$list.pop();
                    row.onRemove();
                }
                self.addFirst();
            }
        }
        self.init($modal);
        return self;
    }

    $(document).ready(function(){
        // элемент email list
        var modal = $('#myModal');
        var emails = new EmailList(modal)
        // создание
        modal.on('show.bs.modal', function (e) {
            emails.template_id = $(e.relatedTarget).data('template');
            $('#template_num').text(emails.template_id);
            emails.clear()
        });

        $('#myModal').on('hide.bs.modal', function (e) {
            if (emails.$list.length > 1 && !confirm("Are you sure you want to close it?")) {
                e.preventDefault();
            }
        });

        $('#test-params').submit(function( event ) {
            event.preventDefault();
            event.stopImmediatePropagation(); // this is required in some cases
            if (emails.validate()) {
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: $(this).serialize(),
                }).done(function(response){
                    location.reload();
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.status );
                })
            }
        });
    });

})(jQuery);