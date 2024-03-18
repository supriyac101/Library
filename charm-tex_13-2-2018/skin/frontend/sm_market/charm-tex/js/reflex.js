﻿var tmp = "Microsoft Internet Explorer" == navigator.appName && 1 > navigator.userAgent.indexOf("Opera") ? 1 : 0; if (tmp) var isIE = document.namespaces && (!document.documentMode || 9 > document.documentMode) ? 1 : 0;
if (isIE && null == document.namespaces.v) { for (var e = "shape,shapetype,group,background,path,formulas,handles,fill,stroke,shadow,textbox,textpath,imagedata,line,polyline,curve,roundrect,oval,rect,arc,image".split(","), s = document.createStyleSheet(), i = 0; i < e.length; i++) s.addRule("v\\:" + e[i], "behavior: url(#default#VML);"); document.namespaces.add("v", "urn:schemas-microsoft-com:vml") }
function getImages(f) { for (var c = document.getElementsByTagName("img"), g = [], a = 0, b, m, l = 0, a = 0; a < c.length; a++) { b = c[a]; m = b.className.split(" "); for (l = 0; l < m.length; l++) if (m[l] == f) { g.push(b); break } } return g } function getClasses(f, c) { for (var g = "", a = 0; a < f.length; a++) f[a] != c && (g && (g += " "), g += f[a]); return g } function getClassValue(f, c) { for (var g = 0, a = c.length, b = 0; b < f.length; b++) if (0 == f[b].indexOf(c)) { g = Math.min(f[b].substring(a), 100); break } return Math.max(0, g) }
function getClassColor(f, c) { for (var g = 0, g = "", a = c.length, b = 0; b < f.length; b++) if (0 == f[b].indexOf(c)) { g = f[b].substring(a); g = "#" + g.toLowerCase(); break } return g.match(/^#[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f]$/i) ? g : 0 } function getClassAttribute(f, c) { for (var g = 0, a = 0; a < f.length; a++) if (0 == f[a].indexOf(c)) { g = 1; break } return g }
function clipPolyRight(f, c, g, a, b, m, l, p) { var d = (b - m - m) / b; f.beginPath(); f.moveTo(c, g); f.lineTo(a, g + m); f.lineTo(a, g + b - m); f.lineTo(c, g + b); 0 < l && (f.lineTo(c, g + b - p), f.lineTo(a, g + b - m - d * p), f.lineTo(a, g + b - m - d * (p + l)), f.lineTo(c, g + b - p - l)); f.closePath() } function clipPolyLeft(f, c, g, a, b, m, l, p) { var d = (b - m - m) / b; f.beginPath(); f.moveTo(c, g + m); f.lineTo(a, g + 1); f.lineTo(a, g + b); f.lineTo(c, g + b - m); 0 < l && (f.lineTo(c, g + b - m - d * p), f.lineTo(a, g + b - p), f.lineTo(a, g + b - p - l), f.lineTo(c, g + b - m - d * (p + l))); f.closePath() }
function strokePolyRight(f, c, g, a, b, m, l, p, d) { var y = (b - m - m) / b, t = 1 <= d ? 1 : 0; f.beginPath(); f.moveTo(c + d, g + d); f.lineTo(a - d, g + m + d - t); f.lineTo(a - d, g + b - m - y * (p + l) - d); f.lineTo(c + d, g + b - p - l - d); f.closePath() } function strokePolyLeft(f, c, g, a, b, m, l, p, d) { var y = (b - m - m) / b, t = 1 <= d ? 1 : 0; f.beginPath(); f.moveTo(c + d, g + m + d - t); f.lineTo(a - d, g + d); f.lineTo(a - d, g + b - p - l - d); f.lineTo(c + d, g + b - m - y * (p + l) - d); f.closePath() }
function clipReflex(f, c, g, a, b, m, l, p, d) { l = (b - m - m) / b; f.beginPath(); "r" == d ? (f.moveTo(c, g + b - p), f.lineTo(a, g + b - m - l * p), f.lineTo(a, g + b - m + 2), f.lineTo(c, g + b + 2)) : (f.moveTo(a, g + b + 2), f.lineTo(a, g + b - p), f.lineTo(c, g + b - m - l * p), f.lineTo(c, g + b - m + 2)); f.closePath() } function clearReflex(f, c, g, a, b, m, l, p, d) { f.beginPath(); "r" == d ? (f.moveTo(c, g + b - 1), f.lineTo(a, g + b - m - 1), f.lineTo(a, g + b - m + 1), f.lineTo(c, g + b + 1)) : (f.moveTo(a, g + b - 1), f.lineTo(c, g + b - m - 1), f.lineTo(c, g + b - m + 1), f.lineTo(a, g + b + 1)); f.closePath() }
function addIEReflex() {
    var f = getImages("reflex"), c, g, a, b, m, l, p, d, y, t, v, h, w, B, q, A, x, r, n, D, o, j, k, u, E, F, z; document.getElementsByTagName("img"); var C = "r"; for (y = 0; y < f.length; y++) if (c = f[y], g = c.parentNode, 32 <= c.width && 32 <= c.height) {
        l = c.className.split(" "); t = getClassValue(l, "iradius"); z = getClassValue(l, "iborder"); a = getClassValue(l, "iheight"); x = getClassValue(l, "iopacity"); h = getClassValue(l, "idistance"); r = getClassColor(l, "icolor"); v = getClassAttribute(l, "itiltleft"); o = getClassAttribute(l, "itiltright"); j = getClassAttribute(l,
"itiltnone"); !0 == o && (C = "r"); !0 == j && (C = "n"); !0 == v && (C = "l"); l = getClasses(l, "reflex"); o = c.height; j = c.width; Math.min(t, Math.max(j, o) / 10); r = 0 != r ? r : "#000000"; x = 0 < x ? x : 33; A = 100 / (10 <= a ? a : 33); q = Math.floor(o / A); 1 == z ? n = 0 : (z = 2 * Math.floor(Math.round(Math.min(Math.min(z, q / 4), Math.max(j, o) / 20)) / 2), n = 0 < z ? z / 2 : 0); k = parseInt(j / 20); v = 1; w = Math.floor((j - k - k) / 12); B = (j - k - k) % 12; D = ((j - k - k) / 12 - 1) / 2; u = w + (0 < B ? 1 : 0); t = (o - u - u) / o; b = "block" == c.currentStyle.display.toLowerCase() ? "block" : "inline-block"; a = document.createElement("" + ('<var style="overflow:hidden;display:' +
b + ";width:" + j + "px;height:" + (o + q + h) + 'px;padding:0;">')); m = c.currentStyle.styleFloat.toLowerCase(); b = "left" == m || "right" == m ? "inline" : b; b = '<v:group style="zoom:1; display:' + b + "; margin:-1px 0 0 -1px; padding:0; position:relative; width:" + j + "px;height:" + (o + q + h) + 'px;" coordsize="' + j + "," + (o + q + h) + '">'; if ("n" == C) p = '<v:rect strokeweight="0" filled="t" stroked="f" fillcolor="#ffffff" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:0px; left:0px; width:' + j + "px;height:" + o + 'px;"><v:fill src="' + c.src +
'" type="frame" /></v:rect>', E = '<v:rect strokeweight="' + z + '" strokecolor="' + r + '" filled="f" stroked="' + (0 < n || 0 < z ? "t" : "f") + '" fillcolor="#ffffff" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + n + "px; left:" + n + "px; width:" + (j - n - n) + "px;height:" + (o - n - n) + 'px;"></v:rect>', F = '<v:rect strokeweight="' + z + '" strokecolor="' + r + '" filled="f" stroked="' + (0 < n || 0 < z ? "t" : "f") + '" fillcolor="#ffffff" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + (o + h + n) + "px; left:" + n + "px; width:" +
(j - n - n) + "px;height:" + (q - n - n) + "px; filter: progid:DXImageTransform.Microsoft.Alpha(opacity=" + x + ",style=1,finishOpacity=0,startx=0,starty=0,finishx=0,finishy=" + parseInt(0.66 * o) + ');"></v:rect>', d = '<v:rect strokeweight="0" filled="t" stroked="f" fillcolor="#ffffff" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + (o + h) + "px; left:0px; width:" + j + "px;height:" + q + "px; filter:flipv progid:DXImageTransform.Microsoft.Alpha(opacity=" + x + ",style=1,finishOpacity=0,startx=0,starty=0,finishx=0,finishy=" +
o + ');"><v:fill origin="0,0" position="0,-' + (A / 2 - 0.5) + '" size="1,' + 1 * A + '" src="' + c.src + '" type="frame" /></v:rect>'; else if ("r" == C) {
            p = '<v:rect strokeweight="0" filled="t" stroked="f" fillcolor="#808080" style="position:absolute; margin:-1px 0 0 -1px;padding:0 ;width:' + j + "px;height:" + (o + q + h) + 'px;"><v:fill color="#808080" opacity="0.0" /></v:rect><v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#ffffff" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + k + ",0 l " + k + "," + o + "," + (j - k) + "," + (o -
u) + "," + (j - k) + "," + u + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:0px; left:0px; width:' + j + "px;height:" + o + 'px;"><v:fill src="' + c.src + '" type="frame" /></v:shape>'; for (d = 0; d < w; d++) d == w - 1 && (v = 0 < B ? 1 : 0), p += '<v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#808080" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + (k + 12 * d) + "," + d + " l " + (v + k + 12 * (d + 1)) + "," + (d + 1) + "," + (v + k + 12 * (d + 1)) + "," + (o - 1 - d) + "," + (k + 12 * d) + "," + (o - d) + ' x e" style="position:absolute; margin: -1px 0 0 -1px; padding:0px; top:0px; left:0px; width:' +
j + "px; height:" + o + 'px;"><v:fill origin="0,0" position="' + (D - d) + ',0" size="' + (j - k - k) / 12 + ',1" type="frame" src="' + c.src + '" /></v:shape>'; 0 < B && (p += '<v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#808080" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + (k + 12 * d) + "," + d + " l " + (k + 12 * (d + 1)) + "," + (d + 1) + "," + (k + 12 * (d + 1)) + "," + (o - 1 - d) + "," + (k + 12 * d) + "," + (o - d) + ' x e" style="position:absolute; margin: -1px 0 0 -1px; padding:0px; top:0px; left:0px; width:' + j + "px; height:" + o + 'px;"><v:fill origin="0,0" position="' +
(D - d) + ',0" size="' + (j - k - k) / 12 + ',1" type="frame" src="' + c.src + '" /></v:shape>'); v = w * t / (o / 100) / 2; 0 < n || 0 < z ? (E = '<v:shape strokeweight="' + z + '" strokecolor="' + r + '" filled="f" stroked="' + (0 < n || 0 < z ? "t" : "f") + '" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + (k + n) + "," + n + " l " + (k + n) + "," + (o - n) + "," + (j - k - n) + "," + (o - u - n) + "," + (j - k - n) + "," + (u + n) + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:0px; left:0px; width:' + j + "px;height:" + o + 'px;"></v:shape>', F = '<v:shape strokeweight="' + z + '" strokecolor="' +
r + '" stroked="' + (0 < n || 0 < z ? "t" : "f") + '" filled="f" coordorigin="0,0" coordsize="' + j + "," + (u + q + h) + '" path="m ' + (k + n) + "," + (u + h + n) + " l " + (k + n) + "," + (q + u + h - n) + "," + (j - k - n) + "," + (parseInt((q + h) * t) - n) + "," + (j - k - n) + "," + (parseInt(h * t) + n) + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + (o - u + h) + "px; left:0px; width:" + j + "px;height:" + (u + q + h) + "px; flip: y; filter:flipv progid:DXImageTransform.Microsoft.Alpha(opacity=" + x + ",style=1,finishOpacity=0,startx=0,starty=0,finishx=" + v + ',finishy=80);"></v:shape>') :
F = E = ""; d = '<v:shape strokeweight="0" stroked="f" filled="t" fillcolor="#808080" coordorigin="0,0" coordsize="' + j + "," + (u + q + h) + '" path="m ' + k + "," + (u + h) + " l " + k + "," + (q + u + h) + "," + (j - k) + "," + parseInt((q + h) * t) + "," + (j - k) + "," + parseInt(h * t) + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + (o - u + h) + "px; left:0px; width:" + j + "px;height:" + (u + q + h) + "px; flip: y; filter:flipv progid:DXImageTransform.Microsoft.Alpha(opacity=" + x + ",style=1,finishOpacity=0,startx=0,starty=0,finishx=" + v + ',finishy=90);"><v:fill origin="0,0" position="0,-' +
(A / 2 - 0.5) + '" size="1,' + A + '" src="' + c.src + '" type="frame" /></v:shape>'
        } else if ("l" == C) {
            p = '<v:rect strokeweight="0" filled="t" stroked="f" fillcolor="#808080" style="position:absolute; margin:-1px 0 0 -1px;padding:0 ;width:' + j + "px;height:" + (o + q + h) + 'px;"><v:fill color="#808080" opacity="0.0" /></v:rect><v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#ffffff" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + k + "," + u + " l " + k + "," + (o - u) + "," + (j - k) + "," + o + "," + (j - k) + ',0 x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:0px; left:0px; width:' +
j + "px;height:" + o + 'px;"><v:fill src="' + c.src + '" type="frame" /></v:shape>'; for (d = 0; d < w; d++) d == w - 1 && (v = 0 < B ? 1 : 0), p += '<v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#808080" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + (k + 12 * d) + "," + (w - d) + " l " + (v + k + 12 * (d + 1)) + "," + (w - 1 - d) + "," + (v + k + 12 * (d + 1)) + "," + (o - 1 - w + d) + "," + (k + 12 * d) + "," + (o - w + d) + ' x e" style="position:absolute; margin: -1px 0 0 -1px; padding:0px; top:0px; left:0px; width:' + j + "px; height:" + o + 'px;"><v:fill origin="0,0" position="' + (D -
d) + ',0" size="' + (j - k - k) / 12 + ',1" type="frame" src="' + c.src + '" /></v:shape>'; 0 < B && (p += '<v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#808080" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + (k + 12 * d) + "," + (w - d) + " l " + (k + 12 * (d + 1)) + "," + (w - 1 - d) + "," + (k + 12 * (d + 1)) + "," + (o - 1 - w + d) + "," + (k + 12 * d) + "," + (o - w + d) + ' x e" style="position:absolute; margin: -1px 0 0 -1px; padding:0px; top:0px; left:0px; width:' + j + "px; height:" + o + 'px;"><v:fill origin="0,0" position="' + (D - d) + ',0" size="' + (j - k - k) / 12 + ',1" type="frame" src="' +
c.src + '" /></v:shape>'); v = 100 - w * t / (o / 100) / 2; 0 < n || 0 < z ? (E = '<v:shape strokeweight="' + z + '" strokecolor="' + r + '" filled="f" stroked="' + (0 < n || 0 < z ? "t" : "f") + '" coordorigin="0,0" coordsize="' + j + "," + o + '" path="m ' + (k + n) + "," + (u + n) + " l " + (k + n) + "," + (o - u - n) + "," + (j - k - n) + "," + (o - n) + "," + (j - k - n) + "," + n + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:0px; left:0px; width:' + j + "px;height:" + o + 'px;"></v:shape>', F = '<v:shape strokeweight="' + z + '" strokecolor="' + r + '" stroked="' + (0 < n || 0 < z ? "t" : "f") + '" filled="f" coordorigin="0,0" coordsize="' +
j + "," + (u + q + h) + '" path="m ' + (k + n) + "," + (parseInt(h * t) + n) + " l " + (k + n) + "," + (parseInt((q + h) * t) - n) + "," + (j - k - n) + "," + (q + u + h - n) + "," + (j - k - n) + "," + (u + h + n) + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + (o - u + h) + "px; left:0px; width:" + j + "px;height:" + (u + q + h) + "px; flip: y; filter:flipv progid:DXImageTransform.Microsoft.Alpha(opacity=" + x + ",style=1,finishOpacity=0,startx=100,starty=0,finishx=" + v + ',finishy=80);"></v:shape>') : F = E = ""; d = '<v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#808080" coordorigin="0,0" coordsize="' +
j + "," + (u + q + h) + '" path="m ' + k + "," + parseInt(h * t) + " l " + k + "," + parseInt((q + h) * t) + "," + (j - k) + "," + (q + u + h) + "," + (j - k) + "," + (u + h) + ' x e" style="position:absolute; margin:-1px 0 0 -1px; padding:0; top:' + (o - u + h) + "px; left:0px; width:" + j + "px;height:" + (u + q + h) + "px; flip: y; filter:flipv progid:DXImageTransform.Microsoft.Alpha(opacity=" + x + ",style=1,finishOpacity=0,startx=100,starty=0,finishx=" + v + ',finishy=90);"><v:fill origin="0,0" position="0,-' + (A / 2 - 0.5) + '" size="1,' + A + '" src="' + c.src + '" type="frame" /></v:shape>'
        } a.innerHTML =
b + d + F + p + E + "</v:group>"; a.className = l; a.style.cssText = c.style.cssText; a.style.height = o + q + h + "px"; a.width = j; a.height = o + q + h; a.style.width = j + "px"; a.src = c.src; a.alt = c.alt; "" != c.id && (a.id = c.id); "" != c.title && (a.title = c.title); "" != c.getAttribute("onclick") && a.setAttribute("onclick", c.getAttribute("onclick")); g.replaceChild(a, c); "r" == C ? C = "n" : "n" == C ? C = "l" : "l" == C && (C = "r"); a.style.visibility = "visible"
    } 
}
function addReflex() {
    var f = getImages("reflex"), c, g, a, b, m, l, p, d, y, t, v, h, w, B, q, A, x; document.getElementsByTagName("img"); var r = "r"; g = -1 != navigator.userAgent.indexOf("WebKit") ? !0 : !1; var n = !0 == g ? window.postMessage ? !1 : window.external ? !1 : !0 : !1, D = !0 == g && window.external ? !0 : !1; for (p = 0; p < f.length; p++) if (c = f[p], g = c.parentNode, a = document.createElement("canvas"), a.getContext && 32 <= c.width && 32 <= c.height) {
        m = c.className.split(" "); y = getClassValue(m, "iradius"); x = getClassValue(m, "iborder"); h = getClassValue(m, "iheight");
        B = getClassValue(m, "iopacity"); d = getClassValue(m, "idistance"); w = getClassColor(m, "icolor"); q = getClassAttribute(m, "itiltleft"); A = getClassAttribute(m, "itiltright"); b = getClassAttribute(m, "itiltnone"); !0 == A && (r = "r"); !0 == b && (r = "n"); !0 == q && (r = "l"); b = getClasses(m, "reflex"); m = c.height; A = c.width; Math.min(y, Math.max(A, m) / 10); y = 0 != w ? w : "#000000"; B = (100 - (0 < B ? B : 33)) / 100; w = 100 / (10 <= h ? h : 33); h = Math.floor(c.height / w); x = Math.round(Math.min(Math.min(x, h / 4), Math.max(A, m) / 20)); q = 0 < x ? x / 2 : 0; a.className = b; a.style.cssText = c.style.cssText;
        a.style.height = m + h + d + "px"; a.width = A; a.style.width = A + "px"; a.height = m + h + d; a.src = c.src; a.alt = c.alt; "" != c.id && (a.id = c.id); "" != c.title && (a.title = c.title); "" != c.getAttribute("onclick") && a.setAttribute("onclick", c.getAttribute("onclick")); t = Math.floor(a.width / 12); v = a.width % 12; if ("l" == r || "r" == r) l = document.createElement("canvas"), l.getContext && (l.style.position = "fixed", l.style.left = "-9999px", l.style.top = "0px", l.height = a.height, l.width = a.width, l.style.height = a.height + "px", l.style.width = a.width + "px", n && g.appendChild(l));
        b = a.getContext("2d"); g.replaceChild(a, c); b.clearRect(0, 0, a.width, a.height); D || (b.globalCompositeOperation = "source-over", b.fillStyle = "rgba(0,0,0,0)", b.fillRect(0, 0, a.width, a.height)); b.save(); b.translate(0, a.height); b.scale(1, -1); b.drawImage(c, 0, -(a.height - h - h - d), a.width, a.height - h - d); b.restore(); 0 < x && (b.strokeStyle = y, b.lineWidth = x, b.beginPath(), b.rect(q, a.height - h + q, a.width - x, h), b.closePath(), b.stroke()); if (!n || "n" == r) b.globalCompositeOperation = "destination-out", y = b.createLinearGradient(0, a.height -
h, 0, a.height), y.addColorStop(1, "rgba(0,0,0,1.0)"), y.addColorStop(0, "rgba(0,0,0," + B + ")"), b.fillStyle = y; n ? (b.beginPath(), b.rect(0, a.height - h, a.width, h), b.closePath(), b.fill()) : b.fillRect(0, a.height - h, a.width, h); b.globalCompositeOperation = "source-over"; b.drawImage(c, 0, 0, A, m); b.save(); n && 0 < d && "n" != r && (b.fillStyle = "#808080", b.fillRect(0, a.height - h - d, a.width, d)); 0 < x && "n" == r && (b.beginPath(), b.rect(q, q, a.width - x, a.height - h - d - x), b.closePath(), b.stroke()); if (("l" == r || "r" == r) && l.getContext) {
            b = l.getContext("2d");
            b.globalCompositeOperation = "source-over"; b.clearRect(0, 0, l.width, l.height); if ("r" == r) { for (c = 0; c < t; c++) b.drawImage(a, 12 * c, 0, 12, l.height, 12 * c, 1 * c, 12, l.height - 2 * c); 0 < v && (v = a.width - 12 * t, b.drawImage(a, 12 * c, 0, v, l.height, 12 * c, 1 * c, v, l.height - 2 * c)) } else { for (c = 0; c < t; c++) b.drawImage(a, 12 * c, 0, 12, l.height, 12 * c, 1 * (t - c), 12, l.height - 2 * (t - c)); 0 < v && (v = a.width - 12 * t, b.drawImage(a, 12 * c, 0, v, l.height, 12 * c, 0, v, l.height)) } b.save(); if (a.getContext && (b = a.getContext("2d"), b.clearRect(0, 0, a.width, a.height), "r" == r ? clipPolyRight(b,
a.width / 20, 0, 0.95 * a.width, a.height, t + (0 < v ? 1 : 0), d, h) : clipPolyLeft(b, a.width / 20, 0, 0.95 * a.width, a.height, t + 1, d, h), b.clip(), b.drawImage(l, parseInt(a.width / 20), 0, parseInt(0.9 * a.width), a.height), b.save(), 0 < x && (b.lineWidth = x, "r" == r ? strokePolyRight(b, a.width / 20, 0, 0.95 * a.width, a.height, t + (0 < v ? 1 : 0), d, h, q) : strokePolyLeft(b, a.width / 20, 0, 0.95 * a.width, a.height, t + (0 < v ? 1 : 0), d, h, q), b.stroke()), n)) b.globalCompositeOperation = "destination-out", y = b.createLinearGradient("l" == r ? a.width : 0, a.height - h, "l" == r ? a.width - parseInt(12 /
w) : parseInt(12 / w), a.height), y.addColorStop(1, "rgba(255,0,0,1.0)"), y.addColorStop(0, "rgba(255,0,0," + B + ")"), b.fillStyle = y, clipReflex(b, a.width / 20, 0, 0.95 * a.width, a.height, t + (0 < v ? 1 : 0), d, h, r), b.fill(), globalCompositeOperation = "source-in", clearReflex(b, a.width / 20, 0, 0.95 * a.width, a.height, t + (0 < v ? 1 : 0), d, h, r), b.clip(), b.clearRect(0, 0, a.width, a.height), b.clearRect(0, 0, a.width, a.height), b.clearRect(0, 0, a.width, a.height), b.clearRect(0, 0, a.width, a.height), n && g.removeChild(l)
        } "r" == r ? r = "n" : "n" == r ? r = "l" : "l" == r && (r =
"r"); b.save(); a.style.visibility = "visible"
    } 
} window.addEventListener ? window.addEventListener("load", addReflex, !1) : window.attachEvent("onload", addIEReflex);