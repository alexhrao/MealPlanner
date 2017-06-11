<?php header('Content-type: text/css'); ?>

@media (min-width: 768px){ .container{ width: 90% !important; } }
@media print{
	a[href]:after{ content: "" !important; }
	.container{ width: 98% !important; }
}

table a, .table a { text-decoration: none !important; }

#children-tabs li a{ display: block !important; }

.hidden{ visibility: hidden !important; }

iframe{ border: none; overflow: auto; }

.tab-content{ padding: 10px 20px; border: 1px solid #DDDDDD; border-top: none; }

#pc-loading{ background: none repeat scroll 0 0 yellow; font-family: arial; left: 10px; margin-top: -10px; opacity: 0.85; position: absolute; top: 20px; width: 150px; }

.navbar a.btn { margin-left: 10px; margin-right: 10px; }

.view-on-click a.btn { max-width: 75px; }

/* prevent prototype conflicts */
li.dropdown{ display: block !important; }

.hspacer-xs{ margin-left: 0.1em; margin-right: 0.1em; }
.hspacer-sm{ margin-left: 0.2em; margin-right: 0.2em; }
.hspacer-md{ margin-left: 0.4em; margin-right: 0.4em; }
.hspacer-lg{ margin-left: 0.8em; margin-right: 0.8em; }
.vspacer-xs{ margin-top: 0.1em; margin-bottom: 0.1em; }
.vspacer-sm{ margin-top: 0.2em; margin-bottom: 0.2em; }
.vspacer-md{ margin-top: 0.4em; margin-bottom: 0.4em; }
.vspacer-lg{ margin-top: 0.8em; margin-bottom: 0.8em; }

div.datePicker{ font-size: 1.3em; }
.always_shown{ display: inline !important; }
.text-bold{ font-weight: bold; }
.text-italic{ font-style: italic; }

.form-control{ width: 90% !important; }
.input-group .form-control{ width: 100% !important; }
.panel .btn{ overflow: hidden; }

.select2-container .select2-choice{ height: 2.4em; line-height: 2.2em; }
.select2-container .select2-choice .select2-arrow b{ background-position: 0 -0.1em; }

.navbar ul.dropdown-menu{ max-height: 400px; overflow-y: auto; }

.search_highlight{ background-color: #ff8c00; color: #ffffff; }

.date_combo { padding-right: 0 !important; }
.date_combo select { width: 100% !important; padding-left: 0; padding-right: 0; }

img[src="blank.gif"] { max-height: 10px !important; }


/* fixes for glyph icons in some themes */
.glyphicon-camera:before { content: "\e046"; }
.glyphicon-lock:before { content: "\e033"; }
.glyphicon-eur:before { content: "\20ac"; }
.glyphicon-calendar:before { content: "\e109"; }
.glyphicon-bell:before { content: "\e123"; }
.glyphicon-wrench:before { content: "\e136"; }
.glyphicon-briefcase:before { content: "\e139"; }

