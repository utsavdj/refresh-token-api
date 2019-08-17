<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>


<table>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>Dear <?php echo $email; ?>,</td>
    </tr>

    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>We received a request to reset your password. Follow the instructions below if this request comes from you.
Ignore the E-Mail if the request to reset your password does not come from you.</td>

    <tr>
          <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>
            Click 'confirm' to set a new password.
        </td>
    </tr>
    <tr>
        <td><a href="<?php echo $verification_link.'/'.$password_reset_code ?>"></a></td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>OR click the url below.</td>
    </tr>
    <tr>
        <td><a href="<?php echo $verification_link.'/'.$password_reset_code;?>"><?php echo $verification_link.'/'.$password_reset_code;?></a></a></td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>If clicking the link doesn't work you can copy the link into your browser window or type it there directly.</td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>Thank you,</td>
    </tr>
    <tr>
        <td>Digital Sherpa Team</td>
    </tr>
    <a href="<?php echo $verification_link ?>"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="Digital Sherpa Logo"></a>
</table>