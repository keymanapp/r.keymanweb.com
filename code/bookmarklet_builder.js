(function() {
  function construct_bookmarklet_code(host, kbdid, langid) {
    var code = `
(function(e) {
  e=document.createElement('script');
  e.type='text/javascript';
  e.onload=() => {
    loadKeymanWebBookmarklet('${kbdid}', '${langid}');
  };
  e.src='${host}/code/bookmarklet_loader.js';
  document.body.appendChild(e);
})()`;

    // Remove new-lines and whitespace from the string; simple minification.
    code = code.replace(/[ \r\n]/g, '');

    code = encodeURIComponent(code);

    return `javascript:void(${code})`;
  }

  function construct_bookmarklet(host, kbdid, langid, kbdname, text) {
    var bml = document.createElement('div'); // We may wish to use shadow-dom in the future,
                                             // which can't be applied to <a> elements.
    bml.classList.add('keyman-bookmarklet');

    // Come Shadow DOM stuff, link the stylesheet under the <div>.

    var link = document.createElement('a'); // the actual, draggable fellow.
    link.text = text || kbdid;
    bml.appendChild(link);

    link.href = construct_bookmarklet_code(host, kbdid, langid);

    // Tracker integration.
    link.onmousedown = function() {
      if(typeof _gaq != 'undefined')
        _gaq.push(['_trackEvent', 'Bookmarklet', 'Installing', langid + ',' + kbdname ]);
    };

    return bml;
  }

  // Only publish the actual bookmarklet builder method.
  window['construct_bookmarklet'] = construct_bookmarklet;
})();