{include file="views/products/components/products_search_form.tpl" dispatch="products.search" collapse=true}

{$settings.yandex_search.general.search_results_code nofilter}

{capture name="mainbox_title"}{__("search_results")}{/capture}
