var ajax_loader = '<img src="/images/ajax-loader.gif" alt="Loading" title="Loading" id="throbber" />';
var ajax_status = '<dd id="ajax_status">'+ajax_loader+'</dd>';
var status_dd = '<dd id="ajax_status">&nbsp;</dd>';
var comment_box = '<textarea rows="10" cols="80" name="comment" id="comment"></textarea>';

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

$(function(){
	var token = readCookie('thp_todo_csrf_cookie');
	var todo = {
		toggle_share_form: function() {
			if($('#share').is(":checked"))
			{
				$("#share_form").show();

				if($("input[name='share_type']").is(":checked"))
				{
					var share_type = $("input[name='share_type']:checked").val();

					if(share_type == "group")
					{
						$(".group_share").show();
						$(".friend_share").hide();
					}
					else if(share_type == "friend")
					{
						$(".friend_share").show();
						$(".group_share").hide();
					}
				}
				else
				{
					$(".friend_share").hide();
					$(".group_share").hide();
				}
			}
			else
			{
				// Reset the form
				$("#share_form").hide();
				$('#friend_share, #group_share').removeAttr('checked');
				$('#group option, #friend option, #friend_perms option, #group_perms option').removeAttr('selected');
				$("#friend_perms, #group_perms").val('-1');
			}
		},

		update_status: function(){
			var s = $('#status option:selected').val();
			var t_id = $('#task_id').val();
			$.post('/task/update_status',
				{status:s, task_id:t_id, csrf_token:token},
				function(response) {
					if(response=='1')
					{
						alert('Task Updated');
					}
				});
		},

		update_category: function(){
			var c = $('#category option:selected').val();
			var t_id = $('#task_id').val();
			$.post('/task/update_category',
				{category:c, task_id:t_id, csrf_token:token},
				function(response){
					if(response=='1')
					{
						alert('Task Updated');
					}
				});
		},

		update_timezone: function() {
			var tz = $("#timezone option:selected").val();
			$.post('/account/update_tz',
				{timezone: tz, csrf_token:token},
				function(response){
					if(response =='1')
					{
						alert('Timezone Updated');
					}
				});
		},

		update_num_format: function(){
			var numf = $("#num_format option:selected").val();
			$.post('/account/update_nf',
				{num_format: numf, csrf_token:token},
				function(response){
					if(response == '1')
					{
						alert('Number format Updated');
					}
				});
		},

		add_task_comment: function() {
			var comm = $("#comment").val();
			var t_id = $("#task_id").val();
			var s = $('#status').val();

			$("#ajax_status").replaceWith(ajax_status);
			$.post(
				'/task/add_task_comment',
				{comment:comm, task_id:t_id, status:s, csrf_token:token},
				function(response){
					if(response == '1')
					{
						var t_id = $("#task_id").val();
						$.get('/index.php/task/get_task_comments/', {task_id:t_id}, function(response){
							$("#task_comment_list").replaceWith(response);
							$("#ajax_status").replaceWith(status_dd);
							$("#comment").cleditor()[0].clear();
						});

						$("#task_comment_list").css("visibility", "visible");
					}
					else
					{
						alert('Error posting new comment');
					}
				}
			);
		},

		friend_search: function() {
			var query = $("#q").val();

			if (query.length < 1) return;

			$.get(
				'/friend/ajax_search/',
				{q:query, csrf_token:token},
				function(response){
					$("#friend_search_results tbody").replaceWith(response);
				}
			);
		},

		friend_request: function() {
			var f_id = $(this).attr('id');
			var fid = f_id.replace('f_','');

			$.post(
				'/friend/add_request/',
				{fid:fid, csrf_token:token},
				function(response)
				{
					if(response == 1)
					{
						alert('Friend Request Sent');
					}
					else if (response == -1)
					{
						alert('You already sent a friend request to this member.');
					}
					else
					{
						alert('Error Sending Friend Request');
					}
				}
			);
		},

		accept_friend_request: function() {
			var id = $(this).attr('id');
			var acc_id = id.replace('af_', '');
			$.post(
				'/friend/accept_request/',
				{aid:acc_id, csrf_token:token},
				function(response)
				{
					if(response == 1)
					{
						alert('Congratulations on the new friend.');
						$("#af_"+acc_id).parent('td').parent('tr').hide();
					}
					else
					{
						alert('Error Accepting Friend Request');
					}
				}
			)
		},

		reject_friend_request: function() {
			var id = $(this).attr('id');
			var acc_id = id.replace('rf_', '');
			$.post(
				'/friend/reject_request/',
				{rid:acc_id, csrf_token:token},
				function(response)
				{
					if(response == 1)
					{
						alert('Friend Request Rejected');
						$("#rf_"+acc_id).parent('td').parent('tr').hide();
					}
					else
					{
						alert('Error Rejecting Friend Request');
					}
				}
			)
		},

		update_checklist: function() {
			//Check the task status
			var status_val = $('#status').val();
			var c_id = $(this).val();
			var tid = $("#task_id").val();

			var ch = ($(this).is(":checked")) ? 1 : 0;


			$.post('/task/update_checklist_item/',
				{check_id:c_id, checked:ch, task_id:tid, csrf_token:token},
				function(r)
				{
					if(r == -1)
					{
						alert('Error updating checklist');
						return;
					}
					else if(r == "first")
					{
						//update status to "In Progress"
						$("#status option:selected").removeAttr("selected");
						$("#status option[value='3']").attr("selected", "selected");
					}
					else if(r == "last")
					{
						//update status to "Completed"
						$("#status option:selected").removeAttr("selected");
						$("#status option[value='2']").attr("selected", "selected");
					}
				}
			)
		},

		delete_group: function() {
			var g_id = $(this).attr('id');
			var gid = g_id.replace('group_', '');

			if(confirm("Are you sure you want to delete this group?"))
			{
				$.post(
					'/group/del_group/'+gid,
					{csrf_token:token},
					function(response)
					{
						if(response == 1)
						{
							alert('Group Deleted');
							document.location='/group/manage';
						}
						else if (response == -1)
						{
							alert('You do not have permission to delete this group.');
						}
						else
						{
							alert('Error attempting to delete group.');
						}
					}
				)
			}
		},

		delete_comment: function(event) {
			event.preventDefault();

			var c_id = $(this).parents('dl').attr('id');
			var cid = c_id.replace('comment_', '');

			var dbool = confirm("Are you sure you want to delete this comment?");
			if(dbool)
			{
				var com = $(this);
				$.post(
					'/task/del_task_comment/',
					{comment_id:cid, csrf_token:token},
					function(response)
					{
						if(response == 1)
						{
							com.parents('dl').hide();
						}
						else if (response == -1)
						{
							alert('You do not have permission to delete this comment.');
						}
						else
						{
							alert('Error attempting to delete comment.');
						}
					}
				)
			}
		},

		delete_category:function() {
			var g_id = $(this).attr('id');
			var gid = g_id.replace('cat_', '');

			var dbool = confirm("Are you sure you want to delete this category?");
			if(dbool)
			{
				$.post(
					'/category/del_sub/'+gid,
					{csrf_token:token},
					function(response)
					{
						if(response == 1)
						{
							alert('Category Deleted');
							document.location='/task/category/list';
						}
						else if (response == -1)
						{
							alert('You do not have permission to delete this category.');
						}
						else
						{
							alert('Error attempting to delete category.');
						}
					}
				)
			}
		},

		add_checklist_item: function() {
			var tid = $("#task_id").val();
			var d = $("#check_desc").val();

			$.post(
				'/task/add_checklist_item/',
				{task_id:tid, desc:d, csrf_token:token},
				function(response)
				{
					if(response == 0)
					{
						alert('That item already exists');
					}
					else if(response == -1)
					{
						alert('Error adding checklist item');
					}
					else
					{
						$("#check_desc").val("");
						$(response).prependTo("#checklist");
					}
				}
			)
		}
	};

	$("#reminder_form").hide();
	$("#share_form").hide();

	// ! Hide or show reminder time form with the checkbox state
	$("#reminder").change(function() {
		($(this).is(":checked"))
			? $("#reminder_form").show()
			: $("#reminder_form").hide();
	});

	// ! Hide or show task share form with the checkbox state
	$("#share").change(todo.toggle_share_form);

	//Update the task status
	$('body').delegate('#status','change', todo.update_status);

	//Update the category
	$('body').delegate('#category','change', todo.update_category);

	//Update the timezone
	$('#timezone').change(todo.update_timezone);

	//Update the number format
	$('#num_format').change(todo.update_num_format);

	//Add CLEditor to add/edit task pages
	$("#desc").cleditor();

	//Toggle the display of the comment form
	$("#toggle_comments").on('click', function() {

		if ($("#add_comment_dl").css('display') == 'none')
		{
			$("#add_comment_dl").show();
			$("#comment").cleditor();
		}
		else
		{
			$("#add_comment_dl").hide();
		}

	});

	//Toggle the display of the checklist form
	$('#toggle_checklist').on('click', function() {
		if ($("#add_checklist_dl").css('display') == 'none')
		{
			$("#add_checklist_dl").show();
		}
		else
		{
			$("#add_checklist_dl").hide();
		}
	});

	//Let's find some friends!
	$("#q").on('keypress keyup', todo.friend_search);

	//Let's request to be someone's friend
	$("body").delegate('.request_sub', 'click', todo.friend_request);

	//Accept a friend request
	$(".accept_request").click(todo.accept_friend_request);

	//Reject a friend request
	$(".reject_request").click(todo.reject_friend_request);

	//Let's delete a group
	$(".del_group").click(todo.delete_group);

	//Let's delete a category
	$(".del_cat").click(todo.delete_category);

	//Shall we add a comment to our task?
	$("#add_task_comment").click(todo.add_task_comment);

	//Let's delete a comment
	$('body').delegate(".comment_del", 'click', todo.delete_comment);

	//Add a checklist item
	$("#add_checklist_item").click(todo.add_checklist_item);

	//Let's do some automatic stuff when checklist items are checked off
	$('body').delegate("#checklist input[type=checkbox]",'change', todo.update_checklist);


	//Hide or show remainder form based on option selected.
	$("input[name='share_type']").change(function() {
		var share_type = $("input:radio[name='share_type']:checked").val();

		if(share_type === "group")
		{
			$(".group_share").show();
			$(".friend_share").hide();
		}
		else if(share_type === "friend")
		{
			$(".friend_share").show();
			$(".group_share").hide();
		}
	});

	//Hide the comment boxes
	$("#add_comment_dl").hide();

	//Hide the checklist form
	$("#add_checklist_dl").hide();

	//Show the reminder time form if the checkbox is checked
	if($("#reminder").is(":checked"))
	{
		$("#reminder_form").show();
	}

	//Show the share task form if the checkbox is checked
	if($("#share").is(":checked"))
	{
		$("#share_form").show();
		//hide share type form until an option is selected
	}

	//Select the correct sharing type
	if($("input:radio[name='share_type']").is(":checked"))
	{
		var share_type = $("input:radio[name='share_type']:checked").val();

		if(share_type === "group")
		{
			$(".group_share").show();
			$(".friend_share").hide();
		}
		else if(share_type === "friend")
		{
			$(".friend_share").show();
			$(".group_share").hide();
		}
	}
	else
	{
		$(".friend_share").hide();
		$(".group_share").hide();
	}

	//Add tabs
	$("#tabs").tabs();

	//Datepicker for adding and editing tasks
	$("#due").datepicker({dateFormat: 'yy-mm-dd'});
});