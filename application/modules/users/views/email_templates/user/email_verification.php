<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<table>
    <tr>
        <td>Dear <?php echo $email; ?>,</td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>
            Welcome to Test. Your online shopping search gateway.
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>
            Your account has been created with Test system. Click 'confirm' to verify your email address.
        </td>
    </tr>
    <tr>
        <td>
            <a href="<?php echo $verification_link.'/'.$email_verification_code ?>">
                <button>
                    Confirm
                </button>
            </a>
        </td>
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
        <td>
            <a href="<?php echo $verification_link.'/'.$email_verification_code;?>">
                <?php echo $verification_link.'/'.$email_verification_code;?>
            </a>
            <br/><br/>
        </td>
    </tr>
    <tr>
        <td>

        </td>
    </tr>
    <tr>
        <td>If clicking the button or link doesn't work you can copy the link into your browser or type it there.</td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>
            Once your email is verified, you can login to the system with your registered credentials.
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>If you have any questions please contact us at support@pasanu.com</td>
    </tr>
    <tr>
        <td><br>Thank you,<br>Test Team</td>
    </tr>
    <a href="<?php echo $verification_link ?>"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="Test Logo"></a>
</table>