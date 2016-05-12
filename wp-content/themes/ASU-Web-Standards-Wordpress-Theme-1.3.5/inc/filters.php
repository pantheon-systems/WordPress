<?php

function asu_ws_search_form () {
  $form = '<form target="_top" action="https://search.asu.edu/search" method="get" name="gs">
            <label class="hidden" for="asu_search_box">Search</label>
            <input name="site" value="default_collection" type="hidden">
            <div class="input-group">
              <input class="form-control" value="' . get_search_query() . '" type="text" name="q" size="32" placeholder="Search ASU" id="asu_search_box" class="asu_search_box" onfocus="ASUHeader.searchFocus(this)" onblur="ASUHeader.searchBlur(this)"> 
              <span class="input-group-btn">
                <input type="submit" value="Search" title="Search" class="asu_search_button btn">
              </span>
            </div>
            <input name="sort" value="date:D:L:d1" type="hidden"> 
            <input name="output" value="xml_no_dtd" type="hidden"> 
            <input name="ie" value="UTF-8" type="hidden"> 
            <input name="oe" value="UTF-8" type="hidden"> 
            <input name="client" value="asu_frontend" type="hidden"> 
            <input name="proxystylesheet" value="asu_frontend" type="hidden">
          </form>';
  return $form;
}

add_filter( 'get_search_form', 'asu_ws_search_form' );