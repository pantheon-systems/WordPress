interface JQueryStatic {
	//These methods are added by the jquery-cookie plugin.
	cookie: (name: string, value?: string, options?: {}) => string;
	removeCookie: (name: string, options?: {}) => boolean;
}