/**
 * Created by Administrator on 2017/10/26.
 */
window.onload = function () {
    (function () {

        changeTitleStyle('title-name', 15, 100, 200);

        function changeTitleStyle(el, textLength, fontSize, lineHeight) {
            if (el) {
                var title = document.getElementById(el);
                if (title.innerText.length > textLength) {
                    title.style.fontSize = fontSize + 'px';
                    title.style.lineHeight = lineHeight + 'px';
                }
            }
        }

    })();
}
