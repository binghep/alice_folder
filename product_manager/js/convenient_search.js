$(document).ready(function(){
	//------------for search on Amazon conveniently---------
		$('#search_on_amazon').click(function(){
			var name=$('#name').html();
			if (name){//if string is not empty
				// alert($name);
				window.open("https://www.amazon.cn/s/url=search-alias%3Daps&field-keywords="+encodeURIComponent(name));
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for search on JD conveniently---------
		$('#search_on_jd').click(function(){
			var name=$('#name').html();
			if (name){//if string is not empty
				// alert($name);
				window.open("http://search.jd.com/Search?enc=utf-8&keyword="+encodeURIComponent(name));
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for search on Taobao conveniently---------
		$('#search_on_taobao').click(function(){
			var name=$('#name').html();
			if (name){//if string is not empty
				// alert($name);
				window.open("https://s.taobao.com/search?q="+encodeURIComponent(name)+"&imgfile=&commend=all&search_type=item&ie=utf8");
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for search on Tmall conveniently---------
		$('#search_on_tmall').click(function(){
			var name=$('#name').html();
			if (name){//if string is not empty
				// alert($name);
				window.open("https://list.tmall.com/search_product.htm?q="+encodeURIComponent(name)+"&type=p");
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for product page on 1661usa conveniently---------
		$('#search_on_1661usa').click(function(){
			var id=$('#product_id').html();
			if (id){//if string is not empty
				// alert($name);
				window.open("https://www.1661usa.com/en/catalog/product/view/id/"+id);
			}else{
				alert('product id cannot be empty');
			}
		});
});