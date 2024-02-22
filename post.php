<button type="button" class="btn btn-default" data-toggle="modal" data-target="#Pa01"></button>
		<div class="modal fade" id="Pa01" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
			  <h3 class="modal-title">{$tweets.tweets_title}</h3>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
			<img src="templates/stroke/01-01.jpg"  style="max-width:100%; height:auto;" class="img-responsive center-block" >
			<hr>
			<p class="text-left">{$tweets.tweets_content}</p>
           </div>
          </div>
        </div>
      </div>
	  <!--格線系統-->
{if $op!=goods_display}
       <div class="row mt-3 pt-5 " id="swContent">
            <div class="col-md-12 text-center d-none d-md-block ">
                <img src="templates/images/LOGONEW.png"
                     class="img-fluid " style="width:50%">
            </div>
        </div>
{/if}
		
	
		<div class="col-12 col-md-12 text-center pt-7" style="border:1px solid #dee2e6; max-width:100%; height:100px; background:#000000">
			<h2><strong><mark>Tweets</mark></strong></h2>
		</div>

	{foreach from=$all_tweets item=tweets}
	

    <div class="col-12 col-md-12  " style="border:1px solid #dee2e6; max-width:100%;height:450px;background:#f5f5f5 ">
        <div >
			
			<div class="pt-7" style=" border-bottom: 1px solid #dee2e6;">
				<h3> <strong> {$tweets.tweets_title}    </strong></h3>
				<span class="text-nowrap"> UserID：{$tweets.user_id} <span>
			</div>
            {if $op==user_pic}<div class="col-md-12 ">{else}
			{if $op!=user_pic}
			<div class="row">
				<div class="col-md-6 " style=""><strong></strong></div>
			</div>
			<div class="col-md-6  pull-left img-thumbnail ">{/if}<a href="index.php?goods_sn={$tweets.goods_sn}">
				<img src="{$tweets.pic}" alt="{$tweets.tweets_sn}" style="width:100%" />
			</a></div>
			<div class="col-md-6 pull-right " >
				<div class="col-md-6" style="max-width:100%;"><strong>{$tweets.tweets_content}</strong></div>
			</div>
			{/if}
		</div>
	</div>
	{/foreach}
	</div>
