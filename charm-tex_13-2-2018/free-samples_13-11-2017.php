<form class="formwidth formwidth1" action="/free-samples-mail.php" method="POST">

<label>Captch Code<font color="#FF0000">*</font></label>
<span><img src="/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>" alt="secure" />
<input type="text" name="code" value="" required oninvalid="this.setCustomValidity('Enter the text in the image')" onchange="try{setCustomValidity('')}catch(e){}" />
<?php
                    if($_GET['error']=='yes')
					{
						//$_SESSION['error']='';
						echo '<span style="color:red;">Enter valid security code!<br /></span>';
					}?>
<span class="caption-cls">We need you to enter this code otherwise spammers use this request form to send unlimitted emails.)</span>	
</span>
<label>Facility name:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" name="facilityname"  required oninvalid="this.setCustomValidity('Please enter facility name')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php if($_POST['facilityname']!=''){echo stripslashes($_POST['facilityname']); $_POST['facilityname']='';}?>"></span>

<label>Contact Name:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" name="contactname" required oninvalid="this.setCustomValidity('Please enter contact name')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php if($_POST['contactname']!=''){echo stripslashes($_POST['contactname']); $_POST['contactname']='';}?>"></span>

<label>Title:</label>
<span><input type="text" size="40" name="title" value="<?php if($_POST['title']!=''){echo stripslashes($_POST['title']); $_POST['title']='';}?>"></span>

<label>Phone Number:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" name="phone" required oninvalid="this.setCustomValidity('Please enter phone')" onchange="try{setCustomValidity('')}catch(e){}"  value="<?php if($_POST['phone']!=''){echo stripslashes($_POST['phone']); $_POST['phone']='';}?>"></span>

<label>Fax Number:</label>
<span><input type="text" size="40" name="fax" value="<?php if($_POST['fax']!=''){echo stripslashes($_POST['fax']); $_POST['fax']='';}?>"></span>

<label>Courrier Address:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" name="add1" required oninvalid="this.setCustomValidity('Please enter address')" onchange="try{setCustomValidity('')}catch(e){}"  value="<?php if($_POST['add1']!=''){echo stripslashes($_POST['add1']); $_POST['add1']='';}?>"><br>
<input type="text" size="40" name="add2" value="<?php if($_POST['add2']!=''){echo stripslashes($_POST['add2']); $_POST['add2']='';}?>"></span>

<label>City:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" name="city" required oninvalid="this.setCustomValidity('Please enter city')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php if($_POST['city']!=''){echo stripslashes($_POST['city']); $_POST['city']='';}?>"></span>

<label>State:<font color="#FF0000"> *</font></label>
<span><input type="state" size="40" name="state" required oninvalid="this.setCustomValidity('Please enter state')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php if($_POST['state']!=''){echo stripslashes($_POST['state']); $_POST['state']='';}?>"></span>

<label>Zipcode:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" name="zip" required oninvalid="this.setCustomValidity('Please enter zip')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php if($_POST['zip']!=''){echo stripslashes($_POST['zip']); $_POST['zip']='';}?>"></span>

<label>Number of Beds:<font color="#FF0000"> *</font></label>
<span><input type="text" size="10" name="beds" required oninvalid="this.setCustomValidity('Please enter beds')" onchange="try{setCustomValidity('')}catch(e){}" value="<?php if($_POST['beds']!=''){echo stripslashes($_POST['beds']); $_POST['beds']='';}?>"></span>

<label>Your E-Mail address:<font color="#FF0000"> *</font></label>
<span><input type="text" name="email" required pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}"  value="<?php if($_POST['email']!=''){echo stripslashes($_POST['email']); $_POST['email']='';}?>" oninvalid="this.setCustomValidity('Please enter email valid email address')" onchange="try{setCustomValidity('')}catch(e){}" /></span>
<br><p><font color="#800000">Samples That You Are Requesting</font> (Style Number &amp; Description )</p>

<label>Sample 1:<font color="#FF0000"> *</font></label>
<span><input type="text" size="40" required oninvalid="this.setCustomValidity('Please enter sample 1')" onchange="try{setCustomValidity('')}catch(e){}" name="sample1" value="<?php if($_POST['sample1']!=''){echo stripslashes($_POST['sample1']); $_POST['sample1']='';}?>"></span>

<label>Sample 2:</label>
<span><input type="text" size="40" name="sample2" value="<?php if($_POST['sample2']!=''){echo stripslashes($_POST['sample2']); $_POST['sample2']='';}?>"></span>

<label>Sample 3:</label>
<span><input type="text" size="40" name="sample3" value="<?php if($_POST['sample3']!=''){echo stripslashes($_POST['sample3']); $_POST['sample3']='';}?>"></span>

<label>Sample 4:</label>
<span><input type="text" size="40" name="sample4" value="<?php if($_POST['sample4']!=''){echo stripslashes($_POST['sample4']); $_POST['sample4']='';}?>"></span>

<label>Sample 5:</label>
<span><input type="text" size="40" name="sample5" value="<?php if($_POST['sample5']!=''){echo stripslashes($_POST['sample5']); $_POST['sample5']='';}?>"></span>
<label>&nbsp;</label>
<span><input type="submit" value="Submit Form">

<input type="hidden" name="to" value="<?php echo $from_email; ?>"></span>
</form>