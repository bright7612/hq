;(function(window) {

  var svgSprite = '<svg>' +
    '' +
    '<symbol id="icon-gou" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M800.06837 245.25068 387.101897 658.217153 223.927537 495.043816c-24.418133-24.416086-64.004733-24.416086-88.422865 0-24.414039 24.415063-24.414039 64.003709 0 88.419795l195.480136 195.48116c13.023618 13.020548 36.738739 18.950607 56.342216 18.022469 19.475564 0.924045 42.872436-5.006014 55.889915-18.024515l445.271226-445.271226c24.418133-24.418133 24.418133-64.004733 0-88.420819C864.072079 220.832548 824.486502 220.832548 800.06837 245.25068z"  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-aixin" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M511.999488 213.006302c0 0-188.970886-152.454204-358.88781 23.023366 0 0-143.214772 131.514289 29.163203 308.510446 82.313727 84.520999 333.700151 333.700151 333.700151 333.700151s245.283426-243.846704 323.797617-329.717444c56.223512-61.491493 150.452617-217.817887 4.668323-335.51652C844.438926 213.006302 660.719648 62.597687 511.999488 213.006302z"  ></path>' +
    '' +
    '<path d="M800.831756 314.308499"  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-coin" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M512 5.12C232.448 5.12 5.12 232.448 5.12 512c0 279.552 227.328 506.88 506.88 506.88 279.552 0 506.88-227.328 506.88-506.88C1018.88 232.448 791.552 5.12 512 5.12zM750.592 449.536 750.592 512 565.248 512l0 94.208 185.344 0 0 62.464L565.248 668.672l0 125.952-92.16 0L473.088 669.696 288.768 669.696l0-62.464 185.344 0L474.112 512 288.768 512l0-62.464 122.88 0-122.88-220.16 92.16 0 122.88 220.16 122.88-220.16 122.88 0-153.6 220.16L750.592 449.536z"  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xia" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M511.700683 639.423111 191.917496 319.596945 319.830771 319.596945 511.700683 511.715521 703.570595 319.596945 831.48387 319.596945Z"  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '</svg>'
  var script = function() {
    var scripts = document.getElementsByTagName('script')
    return scripts[scripts.length - 1]
  }()
  var shouldInjectCss = script.getAttribute("data-injectcss")

  /**
   * document ready
   */
  var ready = function(fn) {
    if (document.addEventListener) {
      if (~["complete", "loaded", "interactive"].indexOf(document.readyState)) {
        setTimeout(fn, 0)
      } else {
        var loadFn = function() {
          document.removeEventListener("DOMContentLoaded", loadFn, false)
          fn()
        }
        document.addEventListener("DOMContentLoaded", loadFn, false)
      }
    } else if (document.attachEvent) {
      IEContentLoaded(window, fn)
    }

    function IEContentLoaded(w, fn) {
      var d = w.document,
        done = false,
        // only fire once
        init = function() {
          if (!done) {
            done = true
            fn()
          }
        }
        // polling for no errors
      var polling = function() {
        try {
          // throws errors until after ondocumentready
          d.documentElement.doScroll('left')
        } catch (e) {
          setTimeout(polling, 50)
          return
        }
        // no errors, fire

        init()
      };

      polling()
        // trying to always fire before onload
      d.onreadystatechange = function() {
        if (d.readyState == 'complete') {
          d.onreadystatechange = null
          init()
        }
      }
    }
  }

  /**
   * Insert el before target
   *
   * @param {Element} el
   * @param {Element} target
   */

  var before = function(el, target) {
    target.parentNode.insertBefore(el, target)
  }

  /**
   * Prepend el to target
   *
   * @param {Element} el
   * @param {Element} target
   */

  var prepend = function(el, target) {
    if (target.firstChild) {
      before(el, target.firstChild)
    } else {
      target.appendChild(el)
    }
  }

  function appendSvg() {
    var div, svg

    div = document.createElement('div')
    div.innerHTML = svgSprite
    svgSprite = null
    svg = div.getElementsByTagName('svg')[0]
    if (svg) {
      svg.setAttribute('aria-hidden', 'true')
      svg.style.position = 'absolute'
      svg.style.width = 0
      svg.style.height = 0
      svg.style.overflow = 'hidden'
      prepend(svg, document.body)
    }
  }

  if (shouldInjectCss && !window.__iconfont__svg__cssinject__) {
    window.__iconfont__svg__cssinject__ = true
    try {
      document.write("<style>.svgfont {display: inline-block;width: 1em;height: 1em;fill: currentColor;vertical-align: -0.1em;font-size:16px;}</style>");
    } catch (e) {
      console && console.log(e)
    }
  }

  ready(appendSvg)


})(window)