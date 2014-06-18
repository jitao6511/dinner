<?php 
$_cartInfo = array();
if($_COOKIE['cart'])
{
	$_cartInfo = json_decode($_COOKIE['cart'],1);
}
?>
<script type="text/javascript">
$(function(){
	var _cart = new CartHelper();
	//点击购物车小图标显示购物车的商品
	$('#basketshow').click(function(){
		if($('#onlineOrder').css('display') == 'none')
		{
			$('#onlineOrder').slideDown('fast');
		}
		else
		{
			$('#onlineOrder').slideUp('fast');
		}
	})

	//清空购物车
	$('#emptyOrder').click(function(){
		$('#OrderBody').html('');
		$('#tbbasket').hide();
		$('#no_send').show();
		$('.addToOrder').removeClass('addMore');
		$('#totalPrice').html(0);
		_cart.Clear();
	})

	//添加商品到购物车
	$('.addToOrder').click(function(){
		var shop_id = $('#shop_id').val();
		if(_cart.Read().shop_id && (parseInt(shop_id) != parseInt(_cart.Read().shop_id)))
		{
			alert('当前购物车里面的菜来自:《'+_cart.Read().shop_name+'》，请清空购物车之后再选菜');
			return;
		}
		else if(!_cart.Read().shop_id)
		{
			_cart.Init(shop_id,$('#shop_name').val());
		}
		
		//获取菜的信息
		var foodInfo = $(this).parent().parent();
		var food = {id:foodInfo.attr('food-foodid'),name:foodInfo.attr('food-foodname'),price:foodInfo.attr('food-price')};
		var _index = _cart.Find(food.id);

		if(_index > -1)
		{	
			//如果已经存在
			var o_num = _cart.Read().Items[_index].Count;
			var n_num = parseInt(o_num) + 1;
			$('#_num_'+food.id).html(n_num);
			_cart.Change(food.id,n_num);
		}
		else
		{
			var html  = '<tr food-id="'+food.id+'" id=food_list_'+food.id+'>';
			html += '<td><p class="food_name">'+food.name+'</p></td>';
			html += '<td>';
			html += '<div class="order_num">';
			html += '<a class="minus" href="javascript:void(0);">&nbsp;</a> ';
			html += '<span id=_num_'+food.id+'>1</span>';
			html += ' <a class="add"  href="javascript:void(0);">&nbsp;</a>';
			html += '</div>';
			html += '</td>';
			html += '<td>'+food.price+'</td>';
			html += '<td><a class="del" href="javascript:void(0);">删除</a></td>';
			html += '</tr>';
	    	$('#OrderBody').append(html);
			_cart.Add(food.id,food.name,1,food.price);
		}

		//更新总的价格
		$('#totalPrice').html(_cart.Read().Total);

		//更改按钮样式
		$(this).addClass('addMore');
		$('#tbbasket').show();
		//样式
		$('#no_send').hide();
		if($('#onlineOrder').css('display') == 'none')
		{
			$('#onlineOrder').slideDown('fast');
		}
	})
	
	//减少购物车里面的数量
	$('.minus').live('click',function(){
		var next = $(this).next();
		var obj = next.parent().parent().parent();
		var food_id = obj.attr('food-id');
		var num = _cart.Read().Items[_cart.Find(food_id)].Count;//获取当前该商品的数量
		if(--num <= 0)
		{
			num = 0;
			obj.remove();
			_cart.Del(food_id);
			$('#liFood_'+food_id).find('.addToOrder').removeClass('addMore');
			if(!_cart.Read().Count)
			{
				$('#no_send').show();
				$('#tbbasket').hide();
			}
		}
		else
		{
			next.html(num);
			//更新购物车的数量
			_cart.Change(food_id,num);
		}
		
		$('#totalPrice').html(_cart.Read().Total);
	})
	
	//增加购物车里面的数量
	$('.add').live('click',function(){
		var prev = $(this).prev();
		var obj = prev.parent().parent().parent();
		var food_id = obj.attr('food-id');
		var num = _cart.Read().Items[_cart.Find(food_id)].Count;
		num++;
		prev.html(num);
		//更新购物车的数量
		_cart.Change(food_id,num);
		$('#totalPrice').html(_cart.Read().Total);
	})
	
	//删除购物车里面的一件商品
	$('.del').live('click',function(){
		var obj = $(this).parent().parent();
		var food_id = obj.attr('food-id');
		obj.remove();
		$('#liFood_'+food_id).find('.addToOrder').removeClass('addMore');
		_cart.Del(food_id);
		
		//如果购物车里面没有商品
		if(!_cart.Read().Count)
		{
			$('#no_send').show();
			$('#tbbasket').hide();
		}
		
		$('#totalPrice').html(_cart.Read().Total);
	})
	
	//点击下单按钮提交查看订单的页面
	$('#ComfirmOrder').click(function(){
		if(!_cart.Read().Count)
		{
			alert('您还没有选择菜，请选完之后再下单');
			return;
		}
		window.location.href=$(this).attr('_href');
	})
	
	if(_cart.Read().Count)
	{
		$('#onlineOrder').show();
		$('#tbbasket').show();
		$('#no_send').hide();
	}
	else
	{
		$('#tbbasket').hide();
		$('#no_send').show();
	}
})
</script>
<div id="school">
   <em>当前餐厅：<?php echo $shop['name'];?></em>
   <input type="hidden" id="shop_id" value="<?php echo $shop['id'];?>" />
   <input type="hidden" id="shop_name" value="<?php echo $shop['name'];?>" />
</div>
<div id="rank" class="clearfix"></div>
<div class="clearfix">
    <div id="left" class="shadow s_menu">          
	    <div id="s_tab">
	        <a href="#" class="active">看菜单</a>
	        <!-- 
	        <a href="#">累积评价</a>
	        <a href="#">给餐厅留言</a>
	         -->
	    </div>
	    <div id="scrollPager">
	                <div class="foodList clearfix">
	                    <div class="foodListImg">
	                          <table cellpadding="0" cellspacing="0">
			                            <tbody>
				                            <?php 
				                         		$_tr_num = count($menus)/3;
				                         		for($i = 0;$i<$_tr_num;$i++):
				                         	?>
				                         	<tr>
				                              		<?php for($j = 0;$j<3;$j++):
				                              				$_index = $i * 3 + $j;
				                                    		if($_index >= (count($menus)))
				                                    		{
				                                    			break;
				                                    		}
				                              		?>	
						                            <td class="foodListItem" id="liFood_<?php echo $menus[$_index]['id'];?>" food-foodid="<?php echo $menus[$_index]['id'];?>" food-foodname="<?php echo $menus[$_index]['name'];?>"  food-price="<?php echo $menus[$_index]['price'];?>" food-foodstate="0" food-foodunit="份" >
						                                <img src="<?php echo $menus[$_index]['index_pic'];?>" width="190px" height="139px" >
						                                <p class="foodTitle">
						                                    <?php echo $menus[$_index]['name'];?><span class="unit">(份)</span>
						                                </p>
						                                <p class="food_remark">
						                                    
														</p>
						                                <p class="price_outer">
						                                    <span class="fr addToOrder">来一份</span>
						                                    <span class="price"><?php echo $menus[$_index]['price'];?>元</span>
						                                </p>
						                            </td>
						                            <?php endfor;?>
						                      </tr>
						                      <?php endfor;?>
			                            </tbody>
	                           </table> 
	                    </div> 
	                </div>
	    </div>
    </div>
    <!--end of left-->
	<div id="right">
        <div class="right_item shadow" style="margin-top: 0px;">
        	<!-- 
             <p class="shop-notice">
                   <i class="icon-shop-notice"></i>
                   123
             </p>
             <p class="shop-sendprice">
                   <i class="icon-shop-sedprice"></i>
                            清华大学12元 北京大学12元起送 林大30元起送
                            
             </p>
              -->
             <p class="shop-info">
                  电话：<?php echo $shop['tel'];?><br>
                  联系人：<?php echo $shop['linkman'];?><br>
                  餐厅地址：<?php echo $shop['address'];?>
             </p>
        </div>
                  
        <div id="foodBasket">
              <div class="right_item shadow" id="onlineOrder" style="display: none; border-top-color: rgb(125, 181, 0);">
                            <table id="tbbasket" cellpadding="0" cellspacing="0" width="100%" style="display: none;">
                                <caption>美食筐</caption>
                                <thead id="OrderHead">
                                    <tr>
                                        <th class="col1">
                                            菜品
                                        </th>
                                        <th class="col2">
                                            份数
                                        </th>
                                        <th class="col3">
                                            单价
                                        </th>
                                        <th class="col4">
                                            <a href="javascript:void(0)" id="emptyOrder">清除</a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="OrderBody">
                                	<?php if($_cartInfo['Items']):?>
                                		<?php foreach($_cartInfo['Items'] AS $k => $v):?>
                                		<tr food-id="<?php echo $v['Id'];?>" id="food_list_<?php echo $v['Id'];?>">
	                                		<td>
	                                			<p class="food_name"><?php echo $v['Name'];?></p>
	                                		</td>
	                                		<td>
		                                		<div class="order_num">
			                                		<a class="minus" href="javascript:void(0);">&nbsp;</a> 
			                                			<span id="_num_<?php echo $v['Id'];?>"><?php echo $v['Count'];?></span> 
			                                		<a class="add" href="javascript:void(0);">&nbsp;</a>
		                                		</div>
	                                		</td>
	                                		<td><?php echo $v['Price'];?></td>
	                                		<td><a class="del" href="javascript:void(0);">删除</a></td>
                                		</tr>
                                		<?php endforeach;?>
                                	<?php endif;?>
                                </tbody>
                                <tfoot id="OrderFoot">
                                    <tr id="delivery" style="display: none;">
                                        <td colspan="4" class="food_name" style="">
                                            
                                        </td>
                                    </tr>
                                    <tr class="last">
                                        <td colspan="2" class="order_total">
                                            <p>总价</p>
                                        </td>
                                        <td>
                                            <p id="totalPrice" class="order_price"><?php echo $_cartInfo['Total'];?></p>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div id="no_send" style="display:none;">
                                <p id="no_send_inner">美食筐是空的！</p>
                            </div>
            	</div>
                <div id="createOrder">
                            <div class="shadow" style="height: 38px; background-color: #2c2c2c;">
                                <i id="basketshow" class="fl">
                                    <img src="<?php echo Yii::app()->baseUrl;?>/assets/images/front/no_food.png" width="28px" height="28px">
                                </i>
                                <a id="ComfirmOrder" href="javascript:void(0);" _href="<?php echo Yii::app()->createUrl('site/lookcart');?>" class="fr">去下单&gt; </a>
                                <a id="seeNum" href="javascript:;" class="fr" style="display: none;">查看电话&gt; </a>
                            </div>
                </div>
          </div>
    </div>
</div>