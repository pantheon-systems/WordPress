jQuery(document).ready( function() {

    var bacs_accounts = jQuery('#bacs_accounts');
    var bacs_accounts_tbody = bacs_accounts.find('tbody');

    bacs_accounts.find('thead tr').append( '<th>'+wcml_data.label+'</th>' );
    bacs_accounts_tbody.find('tr').each( function( index ){
        jQuery(this).append( '<td>'+wcml_data.currencies_dropdown[index]+'</td>');
        jQuery(this).find('.wcml-currencies').attr('name','bacs-currency['+index+']');
    });
    bacs_accounts.find('a.add').on('mouseup', function(){
        setTimeout(function () {
            var size = bacs_accounts_tbody.find('.account').length -1;
            var last_accounts_element = bacs_accounts_tbody.find('tr:last');
            last_accounts_element.append('<td>' + wcml_data.default_dropdown + '</td>');
            last_accounts_element.find('.wcml-currencies').attr('name','bacs-currency['+size+']');
        }, 100);
    });

});