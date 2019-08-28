<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/16/2018
 * Time: 20:30
 */
header('Content-Type: text/javascript');
?>
var loadContent = (
    function () {
        var loaded = [];

        function  addHtml(html, where) {
            if(where) {
                $(where).append(html);
            } else {
                $('body').append(html);
            }
        }
        function addCss(css) {
            $('<style>')
                .text(css)
                .appendTo('head');
        }
        function addJs(js, callback) {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'data:text/javascript;base64,' + btoa(js);
            script.addEventListener('load', callback);
            document.head.appendChild(script);
        }
        return function(path, callback, where) {
            if(loaded.indexOf(path) > -1)  {
                callback();
            } else {
                loaded.push(path);
                $.get(path, function(data) {
                   if(data.html) {
                       addHtml(data.html, where);
                   }
                   if(data.css) {
                       addCss(data.css);
                   }
                   if(data.js) {
                       addJs(data.js, callback);
                   } else {
                       callback();
                   }

                });
            }
        }
    }
)();