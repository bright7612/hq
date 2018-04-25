	var sjzxcount=1;//��������ѭ������
	var sjjhcount=1;//���ݽ���ѭ������
	var fwzxcount=1;//��������ѭ������
		//�������Ķ���
		function sjzxAnimation(){
			if(sjzxcount==5){
				$("img[id^='sjzximg'").each(function(index) {
					$(this).attr("src",$(this).attr('src').toString().replace('_t',''));
				});
				sjzxcount=0;
			}else{
				var imgobj=$("#sjzximg"+sjzxcount);
				$(imgobj).attr("src",$(imgobj).attr('src').toString().replace('.png','_t.png'));
			}
			sjzxcount++;
		}

		//���ݽ�������
		function sjjhAnimation(){
			$("span[id^='sjjh']").each(function(index) {
				$(this).attr("class","block_div");
			});
			$("#sjjh"+sjjhcount).attr("class","block_div_h");
			if(sjjhcount==4){
				sjjhcount=0;
			}
			sjjhcount++;
		}

		//�������߶���
		function fwzxAnimation(){
			$("span[id^='fwzx']").each(function(index) {
				$(this).attr("class","block_div");
			});
			$("#fwzx"+fwzxcount).attr("class","block_div_h");
			if(fwzxcount==4){
				fwzxcount=0;
			}
			fwzxcount++;
		}

		//��������_����
		function  lineAnimation_hr(){
			$("div[id^='rightline']").each(function(index, el) {
				var marginleftsize=parseInt($(this).css("margin-left").replace('px',''));
				var parentWidth=parseInt($(this).parent().css("width").replace('px',''))
				if(marginleftsize>=parentWidth-4){
					$(this).css("margin-left", '0px');
				}else{
					$(this).css("margin-left", marginleftsize+2+'px');
				}
			});
		}

		//��������_����
		function  lineAnimation_hl(){
			$("div[id^='leftline']").each(function(index, el) {
				var parentWidth=parseInt($(this).parent().css("width").replace('px',''))
				var marginleftsize=parseInt($(this).css("margin-left").replace('px',''));
				if(marginleftsize<=0){
					marginleftsize=parentWidth-4;
				}
				$(this).css("margin-left", marginleftsize-2+'px');
			});
		}

		//��������_����
		function  lineAnimation_vu(){
			$("div[id^='upline']").each(function(index, el) {
				var parentheight=parseInt($(this).parent().css("height").replace('px',''))
				var margintopsize=parseInt($(this).css("margin-top").replace('px',''));;
				if(margintopsize<=0){
					margintopsize=parentheight-4;
				}
				$(this).css("margin-top", margintopsize-2+'px');
			});
		}

		//��������_����
		function  lineAnimation_vd(){
			$("div[id^='downline']").each(function(index, el) {
				var parentheight=parseInt($(this).parent().css("height").replace('px',''))
				var margintopsize=parseInt($(this).css("margin-top").replace('px',''));;
				if(margintopsize>=parentheight-4){
					margintopsize=-1;
				}
				$(this).css("margin-top", margintopsize+2+'px');
			});
		}