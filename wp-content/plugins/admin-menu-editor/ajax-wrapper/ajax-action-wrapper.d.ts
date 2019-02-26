// Basic type definitions for the Ajaw AJAX wrapper library 1.0

declare namespace AjawV1 {
	interface RequestParams { [name: string]: any }
	interface SuccessCallback { (data, textStatus: string, jqXHR): string }
	interface ErrorCallback { (data, textStatus: string, jqXHR, errorThrown): string }

	class AjawAjaxAction {
		get(params?: RequestParams, success?: SuccessCallback, error?: ErrorCallback): void;
		post(params?: RequestParams, success?: SuccessCallback, error?: ErrorCallback): void;
		request(params?: RequestParams, success?: SuccessCallback, error?: ErrorCallback, method?: string): void;
	}

	function getAction(action: string): AjawAjaxAction;
}