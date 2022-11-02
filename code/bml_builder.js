(function() {
  // // Live version
  // let HOST = 'https://r.keymanweb.com';

  // Debug / dev version
  let HOST = 'http://r.keymanweb.com.local';

  function construct_bookmarklet_stylesheet() {
    let style = document.createElement('style');

    let imgHref = `${HOST}/code/keymanweb-icon-16.png`;

    let styleSpec = `
    .keyman-bookmarklet a {
      padding: 4px 4px 4px 24px;
      background: #D3DAED url('${imgHref}') 4px 4px no-repeat scroll;
      box-shadow: 3px 3px 6px rgba(50,52,56,0.3);
      text-decoration: none;
      color: #000000;
      font-size: 11px;
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

  let bml_style = {
    element: construct_bookmarklet_stylesheet(),
    isLoaded: false
  }

  function construct_bookmarklet_code(kbdid, langid) {
    let code = `
(function() {
  var e=document.createElement('script');
  e.type='text/javascript';
  e.onload=() => {
    loadBookmarklet('${kbdid}', '${langid}');
  };
  e.src='${HOST}/code/bml_loader.js';
  document.body.appendChild(e);
})()`

    // Remove new-lines from the string, as it'll become the href of a link.
    code.replace('\n', '');

    return `javascript:void(${code})`;
  }

  function construct_bookmarklet(kbdid, langid, kbdname, text) {
    let bml = document.createElement('div'); // We may wish to use shadow-dom in the future,
                                             // which can't be applied to <a> elements.
    bml.classList.add('keyman-bookmarklet');

    // Come Shadow DOM stuff, link the stylesheet under the <div>.

    let link = document.createElement('a'); // the actual, draggable fellow.
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
    }

    return bml;
  }

  // Only publish the actual bookmarklet builder method.
  window['construct_bookmarklet'] = construct_bookmarklet;
})();