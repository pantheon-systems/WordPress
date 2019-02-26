/// <reference path="jquery.d.ts" />

interface JQuery {
	//These method are added by the jquery-form plugin.
	ajaxForm: (options: any) => JQuery;
	resetForm: () => JQuery;
}