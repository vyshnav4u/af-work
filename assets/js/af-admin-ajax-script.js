jQuery(document).ready(function () {
  /**
   * AJAX: Fetch advertiser data for - add new advertiser page
   */
  jQuery("#af_select_agency").change(function () {
    var af_agency_id = jQuery("#af_select_agency").val();
    if (af_agency_id == "") {
      jQuery("#af_select_advertiser").html(
        "<option value=''> Select </option>"
      );
    } else {
      var data = {
        action: "my_advertiser_list",
        _ajax_nonce: af_my_admin_ajax_obj.nonce,
        _af_agency_id: af_agency_id,
      };

      jQuery.post(af_my_admin_ajax_obj.ajax_url, data, function (response) {
        var af_adv_list = JSON.parse(response);

        console.log(af_adv_list);

        for (let i = 0; i < af_adv_list.length; i++) {
          jQuery("#af_select_advertiser").append(
            "<option value=" +
              af_adv_list[i]["id"] +
              ">" +
              af_adv_list[i]["name"] +
              "</option>"
          );
        }
      });
    }
  });

  /**
   * AJX : Verify button in agency registration page - add new agency
   */
  jQuery("#af_verify_client_btn").click(function (e) {
    e.preventDefault();
    var client_secert = jQuery("#af_client_secert").val();
    var client_id = jQuery("#af_client_id").val();
    var data = {
      action: "af_verify_agency",
      _ajax_nonce: af_my_admin_ajax_obj.nonce,
      _client_id: client_id,
      _client_secert: client_secert,
    };

    jQuery.post(af_my_admin_ajax_obj.ajax_url, data, function (res_acc_token) {
      if ("error" in res_acc_token) {
        jQuery(".af_msg_verify").html(
          "<div class='af_error'> Invalid Client Id or Client Secert </div>"
        );
        jQuery("#af_add_user_agency_btn").attr("disabled", true);
        jQuery("#af_add_user_agency_btn").removeClass("af_active_btn");
      }

      if ("access_token" in res_acc_token) {
        jQuery(".af_msg_verify").html(
          "<div class='af_success'> Verified Client &#10003;  <span class='af_change_cli_info'> Change Client Info </span> </div>"
        );
        jQuery("#af_add_user_agency_btn").attr("disabled", false);
        jQuery("#af_client_id").attr("readonly", true);
        jQuery("#af_client_secert").attr("readonly", true);
        jQuery("#af_verify_client_btn").attr("readonly", true);
        jQuery("#af_add_user_agency_btn").addClass("af_active_btn");
      }
    });
  });

  /**
   * Change Client info after client is verified : add agency page
   */
  jQuery("form").on("click", ".af_change_cli_info", function () {
    jQuery("#af_client_id").attr("readonly", false);
    jQuery("#af_client_secert").attr("readonly", false);
    jQuery("#af_verify_client_btn").attr("readonly", false);
    jQuery("#af_add_user_agency_btn").attr("disabled", true);
    jQuery(".af_msg_verify").html("");
    jQuery("#af_add_user_agency_btn").removeClass("af_active_btn");
  });

  /**
   *  Disable Add New User Once it is pressed
   */
  jQuery("#af_add_user_agency_btn").click(function () {
    jQuery(this).hide();
    jQuery("#af_add_user_btn_msg").html(
      "<div class='notice notice-success is-dismissible'> <p> Adding... Please Wait...</p></div>"
    );
  });
});
