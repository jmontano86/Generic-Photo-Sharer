<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/23/2018
 * Time: 7:00
 */

require_once('LoadableContent.php');



$js = <<<JS
$(
  function() {
      window.updateNavbar = (function() {
          var curUser = {username: '', role: ''};
          return function () {
              $.get('assets/actions/get_username.php', function(data) {
                  if(curUser.username != data.username || curUser.role != data.role) {
                    $(document).trigger('userchange');
                    curUser = data;
                  }
                  if(data.username === '') {
                      $('#login_button').removeClass('hidden');
                    
                      $('#register_button').removeClass('hidden');
                      $('#username').text(data).addClass('hidden');
                      $('#verify_button').addClass('hidden');
                      $('#logout_button').addClass('hidden');
                  } else {
                    
                      $('#login_button').addClass('hidden');
                      $('#register_button').addClass('hidden');
                      $('#username').text(data.username).removeClass('hidden');
                      if (data.role === 'user') {
                          $('#verify_button').removeClass('hidden');
                      } else {
                          $('#verify_button').addClass('hidden');
                      }
                      $('#logout_button').removeClass('hidden');
                  }
              });
      }
})();
      $('#login_button').click(function() {
         loadContent('assets/includes/login_content.php', function() {
            login();    
         })
      });
      $('#register_button').click(function() {
          loadContent('assets/includes/register_content.php', function() {
            register();    
         })
      });
      $('#logout_button').click(function() {
          $.get('assets/actions/do_logout.php', function() {
              updateNavbar();
          });
      });
      $('#verify_button').click(function() {
          loadContent('assets/includes/verify_confirm_content.php', function() {
            verify();
          });
      });
      $('#home_button').click(function() {
          $(document).trigger('home');
      });
      $(window).on('focus', function() {
         updateNavbar(); 
      });
      updateNavbar();
  }
);

JS;
$html = <<<HTML
<ul class="toolbar">
    <li class="tool_item_left clickable">
        <span id="home_button" class="tool_item_label">Generic Sharer</span>
    </li>
    <li class="tool_item_right clickable">
        <span id="login_button" class="tool_item_label hidden">Sign In</span>
    </li>
       <li class="tool_item_right clickable">
        <span id="register_button" class="tool_item_label hidden">Sign Up</span>
    </li>
       <li class="tool_item_right clickable">
        <span id="logout_button" class="tool_item_label hidden">Log Out</span>
    </li>
    <li class="tool_item_right clickable">
        <span id="verify_button" class="tool_item_label hidden">Verify your Account</span>
    </li>
    <li class="tool_item_right">
        <span id="username" class="tool_item_label hidden"></span>
    </li>
</ul>
HTML;
$css = <<<CSS
.toolbar {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #333333;
    box-shadow: 0 0 11px 1px lightgray;
}

.tool_item_label {
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

.tool_item_right {
    float: right;
}

.tool_item_left {
    float: left;
}

.clickable:hover {
    background-color: black;
    cursor: pointer;
}

.hidden {
    display: none;
}
CSS;




$obj = new LoadableContent($js, $html, $css);
$obj->load();