(function() {
  // Live version
  var HOST = 'https://r.keymanweb.com';

  // // Debug / dev version
  // var HOST = 'http://r.keymanweb.com.local';

  function construct_bookmarklet_stylesheet() {
    var style = document.createElement('style');

    var imgHref = `${HOST}/code/keymanweb-icon-16.png`;

    var styleSpec = `
    /* Easier to override when less specific */
    .keyman-bookmarklet {
      font-size: 11px;
    }

    .keyman-bookmarklet a {
      padding: 4px 4px 4px 24px;
      background: #D3DAED url('${imgHref}') 4px 4px no-repeat scroll;
      box-shadow: 3px 3px 6px rgba(50,52,56,0.3);
      text-decoration: none;
      color: #000000;
      text-align: left;
      display: inline-block;
      height: 16px;
      overflow: hidden;
      white-space: nowrap;
    }
    `;

    style.id='keyman-bookmarklet-style';
    style.appendChild(document.createTextNode(styleSpec));

    return style;
  }

  var bml_style = {
    element: construct_bookmarklet_stylesheet(),
    isLoaded: false
  };

  function construct_bookmarklet_code(kbdid, langid) {
    var code = `
(function(e) {
  e=document.createElement('script');
  e.type='text/javascript';
  e.onload=() => {
    loadKeymanWebBookmarklet('${kbdid}', '${langid}');
  };
  e.src='${HOST}/code/bookmarklet_loader.js';
  document.body.appendChild(e);
})()`;

    // Remove new-lines and whitespace from the string; simple minification.
    code = code.replace(/[ \r\n]/g, '');

    code = encodeURIComponent(code);

    return `javascript:void(${code})`;
  }

  function construct_bookmarklet(kbdid, langid, kbdname, text) {
    var bml = document.createElement('div'); // We may wish to use shadow-dom in the future,
                                             // which can't be applied to <a> elements.
    bml.classList.add('keyman-bookmarklet');

    // Come Shadow DOM stuff, link the stylesheet under the <div>.

    var link = document.createElement('a'); // the actual, draggable fellow.
    link.text = text || kbdid;
    bml.appendChild(link);

    link.href = construct_bookmarklet_code(kbdid, langid);

    // Tracker integration.
    link.onmousedown = function() {
      if(typeof _gaq != 'undefined')
        _gaq.push(['_trackEvent', 'Bookmarklet', 'Installing', langid + ',' + kbdname ]);
    };

    // Ensure that the relevant stylesheet gets properly linked to the page!
    if(!bml_style.isLoaded) {
      document.head.appendChild(bml_style.element);
      bml_style.isLoaded = true;
    }

    return bml;
  }

  // Only publish the actual bookmarklet builder method.
  window['construct_bookmarklet'] = construct_bookmarklet;
})();