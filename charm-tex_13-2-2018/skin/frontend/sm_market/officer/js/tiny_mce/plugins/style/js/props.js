tinyMCEPopup.requireLangPack();

var defaultFonts = "" + 
	"Arial, Helvetica, sans-serif=Arial, Helvetica, sans-serif;" + 
	"Times New Roman, Times, serif=Times New Roman, Times, serif;" + 
	"Courier New, Courier, mono=Courier New, Courier, mono;" + 
	"Times New Roman, Times, serif=Times New Roman, Times, serif;" + 
	"Georgia, Times New Roman, Times, serif=Georgia, Times New Roman, Times, serif;" + 
	"Verdana, Arial, Helvetica, sans-serif=Verdana, Arial, Helvetica, sans-serif;" + 
	"Geneva, Arial, Helvetica, sans-serif=Geneva, Arial, Helvetica, sans-serif";

var defaultSizes = "9;10;12;14;16;18;24;xx-small;x-small;small;medium;large;x-large;xx-large;smaller;larger";
var defaultMeasurement = "+pixels=px;points=pt;inches=in;centimetres=cm;millimetres=mm;picas=pc;ems=em;exs=ex;%";
var defaultSpacingMeasurement = "pixels=px;points=pt;inches=in;centimetres=cm;millimetres=mm;picas=pc;+ems=em;exs=ex;%";
var defaultIndentMeasurement = "pixels=px;+points=pt;inches=in;centimetres=cm;millimetres=mm;picas=pc;ems=em;exs=ex;%";
var defaultWeight = "normal;bold;bolder;lighter;100;200;300;400;500;600;700;800;900";
var defaultTextStyle = "normal;italic;oblique";
var defaultVariant = "normal;small-caps";
var defaultLineHeight = "normal";
var defaultAttachment = "fixed;scroll";
var defaultRepeat = "no-repeat;repeat;repeat-x;repeat-y";
var defaultPosH = "left;center;right";
var defaultPosV = "top;center;bottom";
var defaultVAlign = "baseline;sub;super;top;text-top;middle;bottom;text-bottom";
var defaultDisplay = "inline;block;list-item;run-in;compact;marker;table;inline-table;table-row-group;table-header-group;table-footer-group;table-row;table-column-group;table-column;table-cell;table-caption;none";
var defaultBorderStyle = "none;solid;dashed;dotted;double;groove;ridge;inset;outset";
var defaultBorderWidth = "thin;medium;thick";
var defaultListType = "disc;circle;square;decimal;lower-roman;upper-roman;lower-alpha;upper-alpha;none";

function init() {
	var ce = document.getElementById('container'), h;

	ce.style.cssText = tinyMCEPopup.getWindowArg('style_text');

	h = getBrowserHTML('background_image_browser','background_image','image','advimage');
	document.getElementById("background_image_browser").innerHTML = h;

	document.getElementById('text_color_pickcontainer').innerHTML = getColorPickerHTML('text_color_pick','text_color');
	document.getElementById('background_color_pickcontainer').innerHTML = getColorPickerHTML('background_color_pick','background_color');
	document.getElementById('border_color_top_pickcontainer').innerHTML = getColorPickerHTML('border_color_top_pick','border_color_top');
	document.getElementById('border_color_right_pickcontainer').innerHTML = getColorPickerHTML('border_color_right_pick','border_color_right');
	document.getElementById('border_color_bottom_pickcontainer').innerHTML = getColorPickerHTML('border_color_bottom_pick','border_color_bottom');
	document.getElementById('border_color_left_pickcontainer').innerHTML = getColorPickerHTML('border_color_left_pick','border_color_left');

	fillSelect(0, 'text_font', 'style_font', defaultFonts, ';', true);
	fillSelect(0, 'text_size', 'style_font_size', defaultSizes, ';', true);
	fillSelect(0, 'text_size_measurement', 'style_font_size_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'text_case', 'style_text_case', "capitalize;uppercase;lowercase", ';', true);
	fillSelect(0, 'text_weight', 'style_font_weight', defaultWeight, ';', true);
	fillSelect(0, 'text_style', 'style_font_style', defaultTextStyle, ';', true);
	fillSelect(0, 'text_variant', 'style_font_variant', defaultVariant, ';', true);
	fillSelect(0, 'text_lineheight', 'style_font_line_height', defaultLineHeight, ';', true);
	fillSelect(0, 'text_lineheight_measurement', 'style_font_line_height_measurement', defaultMeasurement, ';', true);

	fillSelect(0, 'background_attachment', 'style_background_attachment', defaultAttachment, ';', true);
	fillSelect(0, 'background_repeat', 'style_background_repeat', defaultRepeat, ';', true);

	fillSelect(0, 'background_hpos_measurement', 'style_background_hpos_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'background_vpos_measurement', 'style_background_vpos_measurement', defaultMeasurement, ';', true);

	fillSelect(0, 'background_hpos', 'style_background_hpos', defaultPosH, ';', true);
	fillSelect(0, 'background_vpos', 'style_background_vpos', defaultPosV, ';', true);

	fillSelect(0, 'block_wordspacing', 'style_wordspacing', 'normal', ';', true);
	fillSelect(0, 'block_wordspacing_measurement', 'style_wordspacing_measurement', defaultSpacingMeasurement, ';', true);
	fillSelect(0, 'block_letterspacing', 'style_letterspacing', 'normal', ';', true);
	fillSelect(0, 'block_letterspacing_measurement', 'style_letterspacing_measurement', defaultSpacingMeasurement, ';', true);
	fillSelect(0, 'block_vertical_alignment', 'style_vertical_alignment', defaultVAlign, ';', true);
	fillSelect(0, 'block_text_align', 'style_text_align', "left;right;center;justify", ';', true);
	fillSelect(0, 'block_whitespace', 'style_whitespace', "normal;pre;nowrap", ';', true);
	fillSelect(0, 'block_display', 'style_display', defaultDisplay, ';', true);
	fillSelect(0, 'block_text_indent_measurement', 'style_text_indent_measurement', defaultIndentMeasurement, ';', true);

	fillSelect(0, 'box_width_measurement', 'style_box_width_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_height_measurement', 'style_box_height_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_float', 'style_float', 'left;right;none', ';', true);
	fillSelect(0, 'box_clear', 'style_clear', 'left;right;both;none', ';', true);
	fillSelect(0, 'box_padding_left_measurement', 'style_padding_left_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_padding_top_measurement', 'style_padding_top_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_padding_bottom_measurement', 'style_padding_bottom_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_padding_right_measurement', 'style_padding_right_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_margin_left_measurement', 'style_margin_left_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_margin_top_measurement', 'style_margin_top_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_margin_bottom_measurement', 'style_margin_bottom_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'box_margin_right_measurement', 'style_margin_right_measurement', defaultMeasurement, ';', true);

	fillSelect(0, 'border_style_top', 'style_border_style_top', defaultBorderStyle, ';', true);
	fillSelect(0, 'border_style_right', 'style_border_style_right', defaultBorderStyle, ';', true);
	fillSelect(0, 'border_style_bottom', 'style_border_style_bottom', defaultBorderStyle, ';', true);
	fillSelect(0, 'border_style_left', 'style_border_style_left', defaultBorderStyle, ';', true);

	fillSelect(0, 'border_width_top', 'style_border_width_top', defaultBorderWidth, ';', true);
	fillSelect(0, 'border_width_right', 'style_border_width_right', defaultBorderWidth, ';', true);
	fillSelect(0, 'border_width_bottom', 'style_border_width_bottom', defaultBorderWidth, ';', true);
	fillSelect(0, 'border_width_left', 'style_border_width_left', defaultBorderWidth, ';', true);

	fillSelect(0, 'border_width_top_measurement', 'style_border_width_top_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'border_width_right_measurement', 'style_border_width_right_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'border_width_bottom_measurement', 'style_border_width_bottom_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'border_width_left_measurement', 'style_border_width_left_measurement', defaultMeasurement, ';', true);

	fillSelect(0, 'list_type', 'style_list_type', defaultListType, ';', true);
	fillSelect(0, 'list_position', 'style_list_position', "inside;outside", ';', true);

	fillSelect(0, 'positioning_type', 'style_positioning_type', "absolute;relative;static", ';', true);
	fillSelect(0, 'positioning_visibility', 'style_positioning_visibility', "inherit;visible;hidden", ';', true);

	fillSelect(0, 'positioning_width_measurement', 'style_positioning_width_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_height_measurement', 'style_positioning_height_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_overflow', 'style_positioning_overflow', "visible;hidden;scroll;auto", ';', true);

	fillSelect(0, 'positioning_placement_top_measurement', 'style_positioning_placement_top_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_placement_right_measurement', 'style_positioning_placement_right_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_placement_bottom_measurement', 'style_positioning_placement_bottom_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_placement_left_measurement', 'style_positioning_placement_left_measurement', defaultMeasurement, ';', true);

	fillSelect(0, 'positioning_clip_top_measurement', 'style_positioning_clip_top_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_clip_right_measurement', 'style_positioning_clip_right_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_clip_bottom_measurement', 'style_positioning_clip_bottom_measurement', defaultMeasurement, ';', true);
	fillSelect(0, 'positioning_clip_left_measurement', 'style_positioning_clip_left_measurement', defaultMeasurement, ';', true);

	TinyMCE_EditableSelects.init();
	setupFormData();
	showDisabledControls();
}

function setupFormData() {
	var ce = document.getElementById('container'), f = document.forms[0], s, b, i;

	// Setup text fields

	selectByValue(f, 'text_font', ce.style.fontFamily, true, true);
	selectByValue(f, 'text_size', getNum(ce.style.fontSize), true, true);
	selectByValue(f, 'text_size_measurement', getMeasurement(ce.style.fontSize));
	selectByValue(f, 'text_weight', ce.style.fontWeight, true, true);
	selectByValue(f, 'text_style', ce.style.fontStyle, true, true);
	selectByValue(f, 'text_lineheight', getNum(ce.style.lineHeight), true, true);
	selectByValue(f, 'text_lineheight_measurement', getMeasurement(ce.style.lineHeight));
	selectByValue(f, 'text_case', ce.style.textTransform, true, true);
	selectByValue(f, 'text_variant', ce.style.fontVariant, true, true);
	f.text_color.value = tinyMCEPopup.editor.dom.toHex(ce.style.color);
	updateColor('text_color_pick', 'text_color');
	f.text_underline.checked = inStr(ce.style.textDecoration, 'underline');
	f.text_overline.checked = inStr(ce.style.textDecoration, 'overline');
	f.text_linethrough.checked = inStr(ce.style.textDecoration, 'line-through');
	f.text_blink.checked = inStr(ce.style.textDecoration, 'blink');

	// Setup background fields

	f.background_color.value = tinyMCEPopup.editor.dom.toHex(ce.style.backgroundColor);
	updateColor('background_color_pick', 'background_color');
	f.background_image.value = ce.style.backgroundImage.replace(new RegExp("url\\('?([^']*)'?\\)", 'gi'), "$1");
	selectByValue(f, 'background_repeat', ce.style.backgroundRepeat, true, true);
	selectByValue(f, 'background_attachment', ce.style.backgroundAttachment, true, true);
	selectByValue(f, 'background_hpos', getNum(getVal(ce.style.backgroundPosition, 0)), true, true);
	selectByValue(f, 'background_hpos_measurement', getMeasurement(getVal(ce.style.backgroundPosition, 0)));
	selectByValue(f, 'background_vpos', getNum(getVal(ce.style.backgroundPosition, 1)), true, true);
	selectByValue(f, 'background_vpos_measurement', getMeasurement(getVal(ce.style.backgroundPosition, 1)));

	// Setup block fields

	selectByValue(f, 'block_wordspacing', getNum(ce.style.wordSpacing), true, true);
	selectByValue(f, 'block_wordspacing_measurement', getMeasurement(ce.style.wordSpacing));
	selectByValue(f, 'block_letterspacing', getNum(ce.style.letterSpacing), true, true);
	selectByValue(f, 'block_letterspacing_measurement', getMeasurement(ce.style.letterSpacing));
	selectByValue(f, 'block_vertical_alignment', ce.style.verticalAlign, true, true);
	selectByValue(f, 'block_text_align', ce.style.textAlign, true, true);
	f.block_text_indent.value = getNum(ce.style.textIndent);
	selectByValue(f, 'block_text_indent_measurement', getMeasurement(ce.style.textIndent));
	selectByValue(f, 'block_whitespace', ce.style.whiteSpace, true, true);
	selectByValue(f, 'block_display', ce.style.display, true, true);

	// Setup box fields

	f.box_width.value = getNum(ce.style.width);
	selectByValue(f, 'box_width_measurement', getMeasurement(ce.style.width));

	f.box_height.value = getNum(ce.style.height);
	selectByValue(f, 'box_height_measurement', getMeasurement(ce.style.height));
	selectByValue(f, 'box_float', ce.style.cssFloat || ce.style.styleFloat, true, true);

	selectByValue(f, 'box_clear', ce.style.clear, true, true);

	setupBox(f, ce, 'box_padding', 'padding', '');
	setupBox(f, ce, 'box_margin', 'margin', '');

	// Setup border fields

	setupBox(f, ce, 'border_style', 'border', 'Style');
	setupBox(f, ce, 'border_width', 'border', 'Width');
	setupBox(f, ce, 'border_color', 'border', 'Color');

	updateColor('border_color_top_pick', 'border_color_top');
	updateColor('border_color_right_pick', 'border_color_right');
	updateColor('border_color_bottom_pick', 'border_color_bottom');
	updateColor('border_color_left_pick', 'border_color_left');

	f.elements.border_color_top.value = tinyMCEPopup.editor.dom.toHex(f.elements.border_color_top.value);
	f.elements.border_color_right.value = tinyMCEPopup.editor.dom.toHex(f.elements.border_color_right.value);
	f.elements.border_color_bottom.value = tinyMCEPopup.editor.dom.toHex(f.elements.border_color_bottom.value);
	f.elements.border_color_left.value = tinyMCEPopup.editor.dom.toHex(f.elements.border_color_left.value);

	// Setup list fields

	selectByValue(f, 'list_type', ce.style.listStyleType, true, true);
	selectByValue(f, 'list_position', ce.style.listStylePosition, true, true);
	f.list_bullet_image.value = ce.style.listStyleImage.replace(new RegExp("url\\('?([^']*)'?\\)", 'gi'), "$1");

	// Setup box fields

	selectByValue(f, 'positioning_type', ce.style.position, true, true);
	selectByValue(f, 'positioning_visibility', ce.style.visibility, true, true);
	selectByValue(f, 'positioning_overflow', ce.style.overflow, true, true);
	f.positioning_zindex.value = ce.style.zIndex ? ce.style.zIndex : "";

	f.positioning_width.value = getNum(ce.style.width);
	selectByValue(f, 'positioning_width_measurement', getMeasurement(ce.style.width));

	f.positioning_height.value = getNum(ce.style.height);
	selectByValue(f, 'positioning_height_measurement', getMeasurement(ce.style.height));

	setupBox(f, ce, 'positioning_placement', '', '', ['top', 'right', 'bottom', 'left']);

	s = ce.style.clip.replace(new RegExp("rect\\('?([^']*)'?\\)", 'gi'), "$1");
	s = s.replace(/,/g, ' ');

	if (!hasEqualValues([getVal(s, 0), getVal(s, 1), getVal(s, 2), getVal(s, 3)])) {
		f.positioning_clip_top.value = getNum(getVal(s, 0));
		selectByValue(f, 'positioning_clip_top_measurement', getMeasurement(getVal(s, 0)));
		f.positioning_clip_right.value = getNum(getVal(s, 1));
		selectByValue(f, 'positioning_clip_right_measurement', getMeasurement(getVal(s, 1)));
		f.positioning_clip_bottom.value = getNum(getVal(s, 2));
		selectByValue(f, 'positioning_clip_bottom_measurement', getMeasurement(getVal(s, 2)));
		f.positioning_clip_left.value = getNum(getVal(s, 3));
		selectByValue(f, 'positioning_clip_left_measurement', getMeasurement(getVal(s, 3)));
	} else {
		f.positioning_clip_top.value = getNum(getVal(s, 0));
		selectByValue(f, 'positioning_clip_top_measurement', getMeasurement(getVal(s, 0)));
		f.positioning_clip_right.value = f.positioning_clip_bottom.value = f.positioning_clip_left.value;
	}

//	setupBox(f, ce, '', 'border', 'Color');
}

function getMeasurement(s) {
	return s.replace(/^([0-9.]+)(.*)$/, "$2");
}

function getNum(s) {
	if (new RegExp('^(?:[0-9.]+)(?:[a-z%]+)$', 'gi').test(s))
		return s.replace(/[^0-9.]/g, '');

	return s;
}

function inStr(s, n) {
	return new RegExp(n, 'gi').test(s);
}

function getVal(s, i) {
	var a = s.split(' ');

	if (a.length > 1)
		return a[i];

	return "";
}

function setValue(f, n, v) {
	if (f.elements[n].type == "text")