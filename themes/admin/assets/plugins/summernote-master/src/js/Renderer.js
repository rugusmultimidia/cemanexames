define([
  'summernote/core/agent',
  'summernote/core/dom',
  'summernote/core/func'
], function (agent, dom, func) {
  /**
   * @class Renderer
   *
   * renderer
   *
   * rendering toolbar and editable
   */
  var Renderer = function () {

    /**
     * bootstrap button template
     * @private
     * @param {String} label button name
     * @param {Object} [options] button options
     * @param {String} [options.event] data-event
     * @param {String} [options.className] button's class name
     * @param {String} [options.value] data-value
     * @param {String} [options.title] button's title for popup
     * @param {String} [options.dropdown] dropdown html
     * @param {String} [options.hide] data-hide
     */
    var tplButton = function (label, options) {
      var event = options.event;
      var value = options.value;
      var title = options.title;
      var className = options.className;
      var dropdown = options.dropdown;
      var hide = options.hide;

      return '<button type="button"' +
                 ' class="btn btn-default btn-sm btn-small' +
                   (className ? ' ' + className : '') +
                   (dropdown ? ' dropdown-toggle' : '') +
                 '"' +
                 (dropdown ? ' data-toggle="dropdown"' : '') +
                 (title ? ' title="' + title + '"' : '') +
                 (event ? ' data-event="' + event + '"' : '') +
                 (value ? ' data-value=\'' + value + '\'' : '') +
                 (hide ? ' data-hide=\'' + hide + '\'' : '') +
                 ' tabindex="-1">' +
               label +
               (dropdown ? ' <span class="caret"></span>' : '') +
             '</button>' +
             (dropdown || '');
    };

    /**
     * bootstrap icon button template
     * @private
     * @param {String} iconClassName
     * @param {Object} [options]
     * @param {String} [options.event]
     * @param {String} [options.value]
     * @param {String} [options.title]
     * @param {String} [options.dropdown]
     */
    var tplIconButton = function (iconClassName, options) {
      var label = '<i class="' + iconClassName + '"></i>';
      return tplButton(label, options);
    };

    /**
     * bootstrap popover template
     * @private
     * @param {String} className
     * @param {String} content
     */
    var tplPopover = function (className, content) {
      var $popover = $('<div class="' + className + ' popover bottom in" style="display: none;">' +
               '<div class="arrow"></div>' +
               '<div class="popover-content">' +
               '</div>' +
             '</div>');
      
      $popover.find('.popover-content').append(content);
      return $popover;
    };

    /**
     * bootstrap dialog template
     *
     * @param {String} className
     * @param {String} [title='']
     * @param {String} body
     * @param {String} [footer='']
     */
    var tplDialog = function (className, title, body, footer) {
      return '<div class="' + className + ' modal" aria-hidden="false">' +
               '<div class="modal-dialog">' +
                 '<div class="modal-content">' +
                   (title ?
                   '<div class="modal-header">' +
                     '<button type="button" class="close" aria-hidden="true" tabindex="-1">&times;</button>' +
                     '<h4 class="modal-title">' + title + '</h4>' +
                   '</div>' : ''
                   ) +
                   '<div class="modal-body">' + body + '</div>' +
                   (footer ?
                   '<div class="modal-footer">' + footer + '</div>' : ''
                   ) +
                 '</div>' +
               '</div>' +
             '</div>';
    };

    var tplButtonInfo = {
      picture: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'picture-o', {
          event: 'showImageDialog',
          title: lang.image.image,
          hide: true
        });
      },
      link: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'link', {
          event: 'showLinkDialog',
          title: lang.link.link,
          hide: true
        });
      },
      table: function (lang, options) {
        var dropdown = '<ul class="note-table dropdown-menu">' +
                         '<div class="note-dimension-picker">' +
                           '<div class="note-dimension-picker-mousecatcher" data-event="insertTable" data-value="1x1"></div>' +
                           '<div class="note-dimension-picker-highlighted"></div>' +
                           '<div class="note-dimension-picker-unhighlighted"></div>' +
                         '</div>' +
                         '<div class="note-dimension-display"> 1 x 1 </div>' +
                       '</ul>';
        return tplIconButton(options.iconPrefix + 'table', {
          title: lang.table.table,
          dropdown: dropdown
        });
      },
      style: function (lang, options) {
        var items = options.styleTags.reduce(function (memo, v) {
          var label = lang.style[v === 'p' ? 'normal' : v];
          return memo + '<li><a data-event="formatBlock" href="#" data-value="' + v + '">' +
                   (
                     (v === 'p' || v === 'pre') ? label :
                     '<' + v + '>' + label + '</' + v + '>'
                   ) +
                 '</a></li>';
        }, '');

        return tplIconButton(options.iconPrefix + 'magic', {
          title: lang.style.style,
          dropdown: '<ul class="dropdown-menu">' + items + '</ul>'
        });
      },
      fontname: function (lang, options) {
        var realFontList = [];
        var items = options.fontNames.reduce(function (memo, v) {
          if (!agent.isFontInstalled(v) && options.fontNamesIgnoreCheck.indexOf(v) === -1) {
            return memo;
          }
          realFontList.push(v);
          return memo + '<li><a data-event="fontName" href="#" data-value="' + v + '" style="font-family:\'' + v + '\'">' +
                          '<i class="' + options.iconPrefix + 'check"></i> ' + v +
                        '</a></li>';
        }, '');

        var hasDefaultFont = agent.isFontInstalled(options.defaultFontName);
        var defaultFontName = (hasDefaultFont) ? options.defaultFontName : realFontList[0];
          
        var label = '<span class="note-current-fontname">' +
                        defaultFontName +
                     '</span>';
        return tplButton(label, {
          title: lang.font.name,
          dropdown: '<ul class="dropdown-menu">' + items + '</ul>'
        });
      },
      fontsize: function (lang, options) {
        var items = options.fontSizes.reduce(function (memo, v) {
          return memo + '<li><a data-event="fontSize" href="#" data-value="' + v + '">' +
                          '<i class="fa fa-check"></i> ' + v +
                        '</a></li>';
        }, '');

        var label = '<span class="note-current-fontsize">11</span>';
        return tplButton(label, {
          title: lang.font.size,
          dropdown: '<ul class="dropdown-menu">' + items + '</ul>'
        });
      },
      color: function (lang, options) {
        var colorButtonLabel = '<i class="' + options.iconPrefix + 'font" style="color:black;background-color:yellow;"></i>';
        var colorButton = tplButton(colorButtonLabel, {
          className: 'note-recent-color',
          title: lang.color.recent,
          event: 'color',
          value: '{"backColor":"yellow"}'
        });

        var dropdown = '<ul class="dropdown-menu">' +
                         '<li>' +
                           '<div class="btn-group">' +
                             '<div class="note-palette-title">' + lang.color.background + '</div>' +
                             '<div class="note-color-reset" data-event="backColor"' +
                               ' data-value="inherit" title="' + lang.color.transparent + '">' +
                               lang.color.setTransparent +
                             '</div>' +
                             '<div class="note-color-palette" data-target-event="backColor"></div>' +
                           '</div>' +
                           '<div class="btn-group">' +
                             '<div class="note-palette-title">' + lang.color.foreground + '</div>' +
                             '<div class="note-color-reset" data-event="foreColor" data-value="inherit" title="' + lang.color.reset + '">' +
                               lang.color.resetToDefault +
                             '</div>' +
                             '<div class="note-color-palette" data-target-event="foreColor"></div>' +
                           '</div>' +
                         '</li>' +
                       '</ul>';

        var moreButton = tplButton('', {
          title: lang.color.more,
          dropdown: dropdown
        });

        return colorButton + moreButton;
      },
      bold: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'bold', {
          event: 'bold',
          title: lang.font.bold
        });
      },
      italic: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'italic', {
          event: 'italic',
          title: lang.font.italic
        });
      },
      underline: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'underline', {
          event: 'underline',
          title: lang.font.underline
        });
      },
      strikethrough: function (lang) {
        return tplIconButton('fa fa-strikethrough', {
          event: 'strikethrough',
          title: lang.font.strikethrough
        });
      },
      superscript: function (lang) {
        return tplIconButton('fa fa-superscript', {
          event: 'superscript',
          title: lang.font.superscript
        });
      },
      subscript: function (lang) {
        return tplIconButton('fa fa-subscript', {
          event: 'subscript',
          title: lang.font.subscript
        });
      },
      clear: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'eraser', {
          event: 'removeFormat',
          title: lang.font.clear
        });
      },
      ul: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'list-ul', {
          event: 'insertUnorderedList',
          title: lang.lists.unordered
        });
      },
      ol: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'list-ol', {
          event: 'insertOrderedList',
          title: lang.lists.ordered
        });
      },
      paragraph: function (lang, options) {
        var leftButton = tplIconButton(options.iconPrefix + 'align-left', {
          title: lang.paragraph.left,
          event: 'justifyLeft'
        });
        var centerButton = tplIconButton(options.iconPrefix + 'align-center', {
          title: lang.paragraph.center,
          event: 'justifyCenter'
        });
        var rightButton = tplIconButton(options.iconPrefix + 'align-right', {
          title: lang.paragraph.right,
          event: 'justifyRight'
        });
        var justifyButton = tplIconButton(options.iconPrefix + 'align-justify', {
          title: lang.paragraph.justify,
          event: 'justifyFull'
        });

        var outdentButton = tplIconButton(options.iconPrefix + 'outdent', {
          title: lang.paragraph.outdent,
          event: 'outdent'
        });
        var indentButton = tplIconButton(options.iconPrefix + 'indent', {
          title: lang.paragraph.indent,
          event: 'indent'
        });

        var dropdown = '<div class="dropdown-menu">' +
                         '<div class="note-align btn-group">' +
                           leftButton + centerButton + rightButton + justifyButton +
                         '</div>' +
                         '<div class="note-list btn-group">' +
                           indentButton + outdentButton +
                         '</div>' +
                       '</div>';

        return tplIconButton(options.iconPrefix + 'align-left', {
          title: lang.paragraph.paragraph,
          dropdown: dropdown
        });
      },
      height: function (lang, options) {
        var items = options.lineHeights.reduce(function (memo, v) {
          return memo + '<li><a data-event="lineHeight" href="#" data-value="' + parseFloat(v) + '">' +
                          '<i class="' + options.iconPrefix + 'check"></i> ' + v +
                        '</a></li>';
        }, '');

        return tplIconButton(options.iconPrefix + 'text-height', {
          title: lang.font.height,
          dropdown: '<ul class="dropdown-menu">' + items + '</ul>'
        });

      },
      help: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'question', {
          event: 'showHelpDialog',
          title: lang.options.help,
          hide: true
        });
      },
      fullscreen: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'arrows-alt', {
          event: 'fullscreen',
          title: lang.options.fullscreen
        });
      },
      codeview: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'code', {
          event: 'codeview',
          title: lang.options.codeview
        });
      },
      undo: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'undo', {
          event: 'undo',
          title: lang.history.undo
        });
      },
      redo: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'repeat', {
          event: 'redo',
          title: lang.history.redo
        });
      },
      hr: function (lang, options) {
        return tplIconButton(options.iconPrefix + 'minus', {
          event: 'insertHorizontalRule',
          title: lang.hr.insert
        });
      }
    };

    var tplPopovers = function (lang, options) {
      var tplLinkPopover = function () {
        var linkButton = tplIconButton(options.iconPrefix + 'edit', {
          title: lang.link.edit,
          event: 'showLinkDialog',
          hide: true
        });
        var unlinkButton = tplIconButton(options.iconPrefix + 'unlink', {
          title: lang.link.unlink,
          event: 'unlink'
        });
        var content = '<a href="http://www.google.com" target="_blank">www.google.com</a>&nbsp;&nbsp;' +
                      '<div class="note-insert btn-group">' +
                        linkButton + unlinkButton +
                      '</div>';
        return tplPopover('note-link-popover', content);
      };

      var tplImagePopover = function () {
        var fullButton = tplButton('<span class="note-fontsize-10">100%</span>', {
          title: lang.image.resizeFull,
          event: 'resize',
          value: '1'
        });
        var halfButton = tplButton('<span class="note-fontsize-10">50%</span>', {
          title: lang.image.resizeHalf,
          event: 'resize',
          value: '0.5'
        });
        var quarterButton = tplButton('<span class="note-fontsize-10">25%</span>', {
          title: lang.image.resizeQuarter,
          event: 'resize',
          value: '0.25'
        });

        var leftButton = tplIconButton(options.iconPrefix + 'align-left', {
          title: lang.image.floatLeft,
          event: 'floatMe',
          value: 'left'
        });
        var rightButton = tplIconButton(options.iconPrefix + 'align-right', {
          title: lang.image.floatRight,
          event: 'floatMe',
          value: 'right'
        });
        var justifyButton = tplIconButton(options.iconPrefix + 'align-justify', {
          title: lang.image.floatNone,
          event: 'floatMe',
          value: 'none'
        });

        var roundedButton = tplIconButton(options.iconPrefix + 'square', {
          title: lang.image.shapeRounded,
          event: 'imageShape',
          value: 'img-rounded'
        });
        var circleButton = tplIconButton(options.iconPrefix + 'circle-o', {
          title: lang.image.shapeCircle,
          event: 'imageShape',
          value: 'img-circle'
        });
        var thumbnailButton = tplIconButton(options.iconPrefix + 'picture-o', {
          title: lang.image.shapeThumbnail,
          event: 'imageShape',
          value: 'img-thumbnail'
        });
        var noneButton = tplIconButton(options.iconPrefix + 'times', {
          title: lang.image.shapeNone,
          event: 'imageShape',
          value: ''
        });

        var removeButton = tplIconButton(options.iconPrefix + 'trash-o', {
          title: lang.image.remove,
          event: 'removeMedia',
          value: 'none'
        });

        var content = '<div class="btn-group">' + fullButton + halfButton + quarterButton + '</div>' +
                      '<div class="btn-group">' + leftButton + rightButton + justifyButton + '</div>' +
                      '<div class="btn-group">' + roundedButton + circleButton + thumbnailButton + noneButton + '</div>' +
                      '<div class="btn-group">' + removeButton + '</div>';
        return tplPopover('note-image-popover', content);
      };

      var tplAirPopover = function () {
        var $content = $('<div />');
        for (var idx = 0, len = options.airPopover.length; idx < len; idx ++) {
          var group = options.airPopover[idx];
          
          var $group = $('<div class="note-' + group[0] + ' btn-group">');
          for (var i = 0, lenGroup = group[1].length; i < lenGroup; i++) {
            var $button = $(tplButtonInfo[group[1][i]](lang, options));

            $button.attr('data-name', group[1][i]);
            
            $group.append($button);
          }
          $content.append($group);
        }

        return tplPopover('note-air-popover', $content.children());
      };

      var $notePopover = $('<div class="note-popover" />');
      
      $notePopover.append(tplLinkPopover());
      $notePopover.append(tplImagePopover());
      
      if (options.airMode) {
        $notePopover.append(tplAirPopover());
      }
      
      return $notePopover;
    };

    var tplHandles = function () {
      return '<div class="note-handle">' +
               '<div class="note-control-selection">' +
                 '<div class="note-control-selection-bg"></div>' +
                 '<div class="note-control-holder note-control-nw"></div>' +
                 '<div class="note-control-holder note-control-ne"></div>' +
                 '<div class="note-control-holder note-control-sw"></div>' +
                 '<div class="note-control-sizing note-control-se"></div>' +
                 '<div class="note-control-selection-info"></div>' +
               '</div>' +
             '</div>';
    };

    /**
     * shortcut table template
     * @param {String} title
     * @param {String} body
     */
    var tplShortcut = function (title, keys) {
      var keyClass = 'note-shortcut-col col-xs-6 note-shortcut-';
      var body = [];

      for (var i in keys) {
        if (keys.hasOwnProperty(i)) {
          body.push(
            '<div class="' + keyClass + 'key">' + keys[i].kbd + '</div>' +
            '<div class="' + keyClass + 'name">' + keys[i].text + '</div>'
            );
        }
      }

      return '<div class="note-shortcut-row row"><div class="' + keyClass + 'title col-xs-offset-6">' + title + '</div></div>' +
             '<div class="note-shortcut-row row">' + body.join('</div><div class="note-shortcut-row row">') + '</div>';
    };

    var tplShortcutText = function (lang) {
      var keys = [
        { kbd: '⌘ + B', text: lang.font.bold },
        { kbd: '⌘ + I', text: lang.font.italic },
        { kbd: '⌘ + U', text: lang.font.underline },
        { kbd: '⌘ + \\', text: lang.font.clear }
      ];

      return tplShortcut(lang.shortcut.textFormatting, keys);
    };

    var tplShortcutAction = function (lang) {
      var keys = [
        { kbd: '⌘ + Z', text: lang.history.undo },
        { kbd: '⌘ + ⇧ + Z', text: lang.history.redo },
        { kbd: '⌘ + ]', text: lang.paragraph.indent },
        { kbd: '⌘ + [', text: lang.paragraph.outdent },
        { kbd: '⌘ + ENTER', text: lang.hr.insert }
      ];

      return tplShortcut(lang.shortcut.action, keys);
    };

    var tplShortcutPara = function (lang) {
      var keys = [
        { kbd: '⌘ + ⇧ + L', text: lang.paragraph.left },
        { kbd: '⌘ + ⇧ + E', text: lang.paragraph.center },
        { kbd: '⌘ + ⇧ + R', text: lang.paragraph.right },
        { kbd: '⌘ + ⇧ + J', text: lang.paragraph.justify },
        { kbd: '⌘ + ⇧ + NUM7', text: lang.lists.ordered },
        { kbd: '⌘ + ⇧ + NUM8', text: lang.lists.unordered }
      ];

      return tplShortcut(lang.shortcut.paragraphFormatting, keys);
    };

    var tplShortcutStyle = function (lang) {
      var keys = [
        { kbd: '⌘ + NUM0', text: lang.style.normal },
        { kbd: '⌘ + NUM1', text: lang.style.h1 },
        { kbd: '⌘ + NUM2', text: lang.style.h2 },
        { kbd: '⌘ + NUM3', text: lang.style.h3 },
        { kbd: '⌘ + NUM4', text: lang.style.h4 },
        { kbd: '⌘ + NUM5', text: lang.style.h5 },
        { kbd: '⌘ + NUM6', text: lang.style.h6 }
      ];

      return tplShortcut(lang.shortcut.documentStyle, keys);
    };

    var tplExtraShortcuts = function (lang, options) {
      var extraKeys = options.extraKeys;
      var keys = [];

      for (var key in extraKeys) {
        if (extraKeys.hasOwnProperty(key)) {
          keys.push({ kbd: key, text: extraKeys[key] });
        }
      }

      return tplShortcut(lang.shortcut.extraKeys, keys);
    };

    var tplShortcutTable = function (lang, options) {
      var colClass = 'class="note-shortcut note-shortcut-col col-sm-6 col-xs-12"';
      var template = [
        '<div ' + colClass + '>' + tplShortcutAction(lang, options) + '</div>' +
        '<div ' + colClass + '>' + tplShortcutText(lang, options) + '</div>',
        '<div ' + colClass + '>' + tplShortcutStyle(lang, options) + '</div>' +
        '<div ' + colClass + '>' + tplShortcutPara(lang, options) + '</div>'
      ];

      if (options.extraKeys) {
        template.push('<div ' + colClass + '>' + tplExtraShortcuts(lang, options) + '</div>');
      }

      return '<div class="note-shortcut-row row">' +
               template.join('</div><div class="note-shortcut-row row">') +
             '</div>';
    };

    var replaceMacKeys = function (sHtml) {
      return sHtml.replace(/⌘/g, 'Ctrl').replace(/⇧/g, 'Shift');
    };

    var tplDialogInfo = {
      image: function (lang, options) {
        var imageLimitation = '';
        if (options.maximumImageFileSize) {
          var unit = Math.floor(Math.log(options.maximumImageFileSize) / Math.log(1024));
          var readableSize = (options.maximumImageFileSize / Math.pow(1024, unit)).toFixed(2) * 1 +
                             ' ' + ' KMGTP'[unit] + 'B';
          imageLimitation = '<small>' + lang.image.maximumFileSize + ' : ' + readableSize + '</small>';
        }

        var body = '<div class="form-group row-fluid note-group-select-from-files">' +
                     '<label>' + lang.image.selectFromFiles + '</label>' +
                     '<input class="note-image-input" type="file" name="files" accept="image/*" multiple="multiple" />' +
                     imageLimitation +
                   '</div>' +
                   '<div class="form-group row-fluid">' +
                     '<label>' + lang.image.url + '</label>' +
                     '<input class="note-image-url form-control span12" type="text" />' +
                   '</div>';
        var footer = '<button href="#" class="btn btn-primary note-image-btn disabled" disabled>' + lang.image.insert + '</button>';
        return tplDialog('note-image-dialog', lang.image.insert, body, footer);
      },

      link: function (lang, options) {
        var body = '<div class="form-group row-fluid">' +
                     '<label>' + lang.link.textToDisplay + '</label>' +
                     '<input class="note-link-text form-control span12" type="text" />' +
                   '</div>' +
                   '<div class="form-group row-fluid">' +
                     '<label>' + lang.link.url + '</label>' +
                     '<input class="note-link-url form-control span12" type="text" />' +
                   '</div>' +
                   (!options.disableLinkTarget ?
                     '<div class="checkbox">' +
                       '<label>' + '<input type="checkbox" checked> ' +
                         lang.link.openInNewWindow +
                       '</label>' +
                     '</div>' : ''
                   );
        var footer = '<button href="#" class="btn btn-primary note-link-btn disabled" disabled>' + lang.link.insert + '</button>';
        return tplDialog('note-link-dialog', lang.link.insert, body, footer);
      },

      help: function (lang, options) {
        var body = '<a class="modal-close pull-right" aria-hidden="true" tabindex="-1">' + lang.shortcut.close + '</a>' +
                   '<div class="title">' + lang.shortcut.shortcuts + '</div>' +
                   (agent.isMac ? tplShortcutTable(lang, options) : replaceMacKeys(tplShortcutTable(lang, options))) +
                   '<p class="text-center">' +
                     '<a href="//summernote.org/" target="_blank">Summernote @VERSION</a> · ' +
                     '<a href="//github.com/summernote/summernote" target="_blank">Project</a> · ' +
                     '<a href="//github.com/summernote/summernote/issues" target="_blank">Issues</a>' +
                   '</p>';
        return tplDialog('note-help-dialog', '', body, '');
      }
    };

    var tplDialogs = function (lang, options) {
      var dialogs = '';

      $.each(tplDialogInfo, function (idx, tplDialog) {
        dialogs += tplDialog(lang, options);
      });

      return '<div class="note-dialog">' + dialogs + '</div>';
    };

    var tplStatusbar = function () {
      return '<div class="note-resizebar">' +
               '<div class="note-icon-bar"></div>' +
               '<div class="note-icon-bar"></div>' +
               '<div class="note-icon-bar"></div>' +
             '</div>';
    };

    var representShortcut = function (str) {
      if (agent.isMac) {
        str = str.replace('CMD', '⌘').replace('SHIFT', '⇧');
      }

      return str.replace('BACKSLASH', '\\')
                .replace('SLASH', '/')
                .replace('LEFTBRACKET', '[')
                .replace('RIGHTBRACKET', ']');
    };

    /**
     * createTooltip
     *
     * @param {jQuery} $container
     * @param {Object} keyMap
     * @param {String} [sPlacement]
     */
    var createTooltip = function ($container, keyMap, sPlacement) {
      var invertedKeyMap = func.invertObject(keyMap);
      var $buttons = $container.find('button');

      $buttons.each(function (i, elBtn) {
        var $btn = $(elBtn);
        var sShortcut = invertedKeyMap[$btn.data('event')];
        if (sShortcut) {
          $btn.attr('title', function (i, v) {
            return v + ' (' + representShortcut(sShortcut) + ')';
          });
        }
      // bootstrap tooltip on btn-group bug
      // https://github.com/twbs/bootstrap/issues/5687
      }).tooltip({
        container: 'body',
        trigger: 'hover',
        placement: sPlacement || 'top'
      }).on('click', function () {
        $(this).tooltip('hide');
      });
    };

    // createPalette
    var createPalette = function ($container, options) {
      var colorInfo = options.colors;
      $container.find('.note-color-palette').each(function () {
        var $palette = $(this), eventName = $palette.attr('data-target-event');
        var paletteContents = [];
        for (var row = 0, lenRow = colorInfo.length; row < lenRow; row++) {
          var colors = colorInfo[row];
          var buttons = [];
          for (var col = 0, lenCol = colors.length; col < lenCol; col++) {
            var color = colors[col];
            buttons.push(['<button type="button" class="note-color-btn" style="background-color:', color,
                           ';" data-event="', eventName,
                           '" data-value="', color,
                           '" title="', color,
                           '" data-toggle="button" tabindex="-1"></button>'].join(''));
          }
          paletteContents.push('<div class="note-color-row">' + buttons.join('') + '</div>');
        }
        $palette.html(paletteContents.join(''));
      });
    };

    /**
     * create summernote layout (air mode)
     *
     * @param {jQuery} $holder
     * @param {Object} options
     */
    this.createLayoutByAirMode = function ($holder, options) {
      var langInfo = options.langInfo;
      var keyMap = options.keyMap[agent.isMac ? 'mac' : 'pc'];
      var id = func.uniqueId();

      $holder.addClass('note-air-editor note-editable');
      $holder.attr({
        'id': 'note-editor-' + id,
        'contentEditable': true
      });

      var body = document.body;

      // create Popover
      var $popover = $(tplPopovers(langInfo, options));
      $popover.addClass('note-air-layout');
      $popover.attr('id', 'note-popover-' + id);
      $popover.appendTo(body);
      createTooltip($popover, keyMap);
      createPalette($popover, options);

      // create Handle
      var $handle = $(tplHandles());
      $handle.addClass('note-air-layout');
      $handle.attr('id', 'note-handle-' + id);
      $handle.appendTo(body);

      // create Dialog
      var $dialog = $(tplDialogs(langInfo, options));
      $dialog.addClass('note-air-layout');
      $dialog.attr('id', 'note-dialog-' + id);
      $dialog.find('button.close, a.modal-close').click(function () {
        $(this).closest('.modal').modal('hide');
      });
      $dialog.appendTo(body);
    };

    /**
     * create summernote layout (normal mode)
     *
     * @param {jQuery} $holder
     * @param {Object} options
     */
    this.createLayoutByFrame = function ($holder, options) {
      var langInfo = options.langInfo;

      //01. create Editor
      var $editor = $('<div class="note-editor"></div>');
      if (options.width) {
        $editor.width(options.width);
      }

      //02. statusbar (resizebar)
      if (options.height > 0) {
        $('<div class="note-statusbar">' + (options.disableResizeEditor ? '' : tplStatusbar()) + '</div>').prependTo($editor);
      }

      //03. create Editable
      var isContentEditable = !$holder.is(':disabled');
      var $editable = $('<div class="note-editable" contentEditable="' + isContentEditable + '"></div>')
          .prependTo($editor);
      if (options.height) {
        $editable.height(options.height);
      }
      if (options.direction) {
        $editable.attr('dir', options.direction);
      }
      var placeholder = $holder.attr('placeholder') || options.placeholder;
      if (placeholder) {
        $editable.attr('data-placeholder', placeholder);
      }

      $editable.html(dom.html($holder));

      //031. create codable
      $('<textarea class="note-codable"></textarea>').prependTo($editor);

      //04. create Toolbar
      var $toolbar = $('<div class="note-toolbar btn-toolbar" />');
      for (var idx = 0, len = options.toolbar.length; idx < len; idx ++) {
        var groupName = options.toolbar[idx][0];
        var groupButtons = options.toolbar[idx][1];

        var $group = $('<div class="note-' + groupName + ' btn-group" />');
        for (var i = 0, btnLength = groupButtons.length; i < btnLength; i++) {
          var buttonInfo = tplButtonInfo[groupButtons[i]];
          // continue creating toolbar even if a button doesn't exist
          if (!$.isFunction(buttonInfo)) { continue; }

          var $button = $(buttonInfo(langInfo, options));
          $button.attr('data-name', groupButtons[i]);  // set button's alias, becuase to get button element from $toolbar
          $group.append($button);
        }
        $toolbar.append($group);
      }
      
      $toolbar.prependTo($editor);
      var keyMap = options.keyMap[agent.isMac ? 'mac' : 'pc'];
      createPalette($toolbar, options);
      createTooltip($toolbar, keyMap, 'bottom');

      //05. create Popover
      var $popover = $(tplPopovers(langInfo, options)).prependTo($editor);
      createPalette($popover, options);
      createTooltip($popover, keyMap);

      //06. handle(control selection, ...)
      $(tplHandles()).prependTo($editor);

      //07. create Dialog
      var $dialog = $(tplDialogs(langInfo, options)).prependTo($editor);
      $dialog.find('button.close, a.modal-close').click(function () {
        $(this).closest('.modal').modal('hide');
      });

      //08. create Dropzone
      $('<div class="note-dropzone"><div class="note-dropzone-message"></div></div>').prependTo($editor);

      //09. Editor/Holder switch
      $editor.insertAfter($holder);
      $holder.hide();
    };

    this.hasNoteEditor = function ($holder) {
      return this.noteEditorFromHolder($holder).length > 0;
    };

    this.noteEditorFromHolder = function ($holder) {
      if ($holder.hasClass('note-air-editor')) {
        return $holder;
      } else if ($holder.next().hasClass('note-editor')) {
        return $holder.next();
      } else {
        return $();
      }
    };

    /**
     * create summernote layout
     *
     * @param {jQuery} $holder
     * @param {Object} options
     */
    this.createLayout = function ($holder, options) {
      if (options.airMode) {
        this.createLayoutByAirMode($holder, options);
      } else {
        this.createLayoutByFrame($holder, options);
      }
    };

    /**
     * returns layoutInfo from holder
     *
     * @param {jQuery} $holder - placeholder
     * @return {Object}
     */
    this.layoutInfoFromHolder = function ($holder) {
      var $editor = this.noteEditorFromHolder($holder);
      if (!$editor.length) {
        return;
      }

      // connect $holder to $editor
      $editor.data('holder', $holder);

      return dom.buildLayoutInfo($editor);
    };

    /**
     * removeLayout
     *
     * @param {jQuery} $holder - placeholder
     * @param {Object} layoutInfo
     * @param {Object} options
     *
     */
    this.removeLayout = function ($holder, layoutInfo, options) {
      if (options.airMode) {
        $holder.removeClass('note-air-editor note-editable')
               .removeAttr('id contentEditable');

        layoutInfo.popover().remove();
        layoutInfo.handle().remove();
        layoutInfo.dialog().remove();
      } else {
        $holder.html(layoutInfo.editable().html());

        layoutInfo.editor().remove();
        $holder.show();
      }
    };

    /**
     *
     * @return {Object}
     * @return {function(label, options=):string} return.button {@link #tplButton function to make text button}
     * @return {function(iconClass, options=):string} return.iconButton {@link #tplIconButton function to make icon button}
     * @return {function(className, title=, body=, footer=):string} return.dialog {@link #tplDialog function to make dialog}
     */
    this.getTemplate = function () {
      return {
        button: tplButton,
        iconButton: tplIconButton,
        dialog: tplDialog
      };
    };

    /**
     * add button information
     *
     * @param {String} name button name
     * @param {Function} buttonInfo function to make button, reference to {@link #tplButton},{@link #tplIconButton}
     */
    this.addButtonInfo = function (name, buttonInfo) {
      tplButtonInfo[name] = buttonInfo;
    };

    /**
     *
     * @param {String} name
     * @param {Function} dialogInfo function to make dialog, reference to {@link #tplDialog}
     */
    this.addDialogInfo = function (name, dialogInfo) {
      tplDialogInfo[name] = dialogInfo;
    };
  };

  return Renderer;
});
