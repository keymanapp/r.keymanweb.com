function loadBookmarklet(kbdid, langid) {
  var loader = () => window['keyman'].addKeyboards(kbdid + "@" + langid);

  if(window['keyman'] instanceof Promise) {
    window['keyman'].then(loader);
  } else if(window['keyman'] instanceof Object) {
    loader();
  } else {
    window['keyman'] = new Promise((resolve, reject) => {
      try {
        var e = document.createElement('script');
        e.type='text/javascript';
        e.onload = resolve;
        e.onerror = reject;
        e.src='https://r.keymanweb.com/code/bml20.php?langid=' + langid + '&keyboard=' + kbdid;
        document.body.appendChild(e);
      } catch(v) {};
    });
  }
}