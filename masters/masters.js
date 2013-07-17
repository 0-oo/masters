var whereIndex = null;

$(function() {
	setUpSearch();
	setUpTable();
});

// 検索フォームを動的に増減させる
function setUpSearch() {
	function checkForm() {
		$("#where-search").prop("disabled", $("button.where-minus").length == 1);
	}
	
	checkForm();
	whereIndex = $("#where-index").val();
	
	$("button.where-minus").on("click", function() {
		$(this).parent().remove();
	});
	
	$("#where-plus").on("click", function() {
		var where = $("#where").clone(true).attr("id", null)
		$(this).parent().before(where);
		var inputs = where.children();
		inputs.eq(1).attr("name", "column_" + whereIndex);
		inputs.eq(2).attr("name", "value_" + whereIndex);
		inputs.eq(3).attr("name", "condition_" + whereIndex);
		checkForm();
		whereIndex++;
	});
}

// 一覧を編集可能にする
function setUpTable() {
	var table = $("#data-list");
	
	// ダブルクリックしたら編集できる
	table.find("tbody td").on("dblclick", edit);
	
	// 新規の場合は全ての列を編集モードにする
	table.find("tfoot td").on("dblclick", function() {
		var tr = $(this).parent();
		tr.children("td").each(edit);
		tr.children("td:first").children("input, select").focus();
	});
	
	// 編集
	function edit() {
		var td = $(this);
		
		if (td.children("input, select").get(0)) {
			return;	// 既に入力モードなら何もしない
		}
		
		var tr = td.parent();
		var th = table.find("th:eq(" + tr.children().index(this) + ")");
		var columnName = th.attr("data-column-name");
		var input = $("#" + columnName).clone();
		
		if (!input.get(0)) {	// selectでない場合
			input = $('<input type="text" />')
				.addClass(td.attr("class"))
				.width(td.width() + 1)
				.attr("pattern", th.attr("data-pattern"))
				.attr("title", th.attr("data-title"))
				.css("ime-mode", th.attr("data-han") ? "inactive" : "active");
		}
		
		input
			.val(td.text())
			.attr("name", columnName)
			.attr("data-before", td.text())
			.prop("required", th.attr("data-required"));
		td
			.text("")
			.addClass("has-input")
			.append(input);
		input.focus();
		toggleButtons(tr, false);
	}
	
	// ボタンの切り替え
	function toggleButtons(tr, init) {
		var buttons = tr.children("th.data-buttons");
		buttons.children().prop("disabled", init);
		buttons.children("button.btn-remove").prop("disabled", !init);
	}
	
	// DB更新
	table.find("th.data-buttons button").on("click", function() {
		var button = $(this);
		var tr = button.closest("tr");
		var action;
		
		if (button.attr("id")) {
			action = "add";
		} else if (button.hasClass("btn-ok")) {
			action = "update";
		} else if (button.hasClass("btn-remove")) {
			action = "remove";
			
			if (!confirm("削除してよろしいですか？")) {
				return;
			}
		} else {	// cancel
			complete(tr, false);
			return;
		}
		
		var values = {}
		
		if (action != "add") {	// キーをセット
			values[table.find("th:first").attr("data-column-name")] = tr.children("th:first").text();
		}
		
		if (action != "remove") {
			var valid = true;
			
			tr.children("td.has-input").each(function() {
				var input = $(this).children();
				values[input.attr("name")] = input.val();
				
				if (input.get(0) && !input.get(0).checkValidity()) {
					input.focus();
					valid = false;
					return false;
				}
			});
			
			if (!valid) {
				return;
			}
		}
		
		button.prop("disabled", true);
		var url = location.href.split("#")[0];
		
		$.post(
			url + "/" + action,
			values,
			function(data) {
				if (data.error) {
					alert(data.error);
					button.prop("disabled", false);
					return;
				}
				
				switch (action) {
					case "add":
						alert("登録しました");
						location.href = url + "#" + data.key;
						location.reload();
						break;
					case "update":
						complete(tr, true);
						alert("更新しました");
						break;
					case "remove":
						tr.remove();
						alert("削除しました");
						break;
				}
			},
			"json"
		);
	});
	
	var key = location.href.split("#")[1];
	
	// 新規登録した場合はそこを表示する
	if (key) {
		var top = null;
		
		table.find("tbody th").each(function() {
			var self = $(this);
			
			if (self.text() == key) {
				top = self.offset().top;
				return false;
			}
		});
		
		if (top) {
			$("html, body").animate({ scrollTop: top - $("div.navbar").height() - 20, scrollLeft: 0 }, "fast");
		}
	}
	
	// 更新 or キャンセルを完了する
	function complete(tr, ok) {
		tr.children("td.has-input").each(function() {
			var td = $(this);
			var input = td.children();
			td
				.empty()
				.removeClass("has-input")
				.text(ok ? input.val() : input.attr("data-before"));
		});
		
		toggleButtons(tr, true);
	}
}
