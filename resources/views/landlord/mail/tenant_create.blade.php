<p>Dear {{$tenant_data['company_name']}},</p>
<p>Welcome to {{$tenant_data['superadmin_company_name']}}! We are thrilled to have you on board as a valued subscriber and want to extend a warm welcome to our platform. Thank you for choosing us as your trusted solution for POS and inventory management.</p>
<p>To help you get started smoothly, we have prepared a quick guide to walk you through the initial setup process and ensure you maximize the benefits of your subscription. Here are the key steps to begin your journey with {{$tenant_data['superadmin_company_name']}}:</p>
<p><strong>Step 1: Login to the system</strong><br>
You can logged in to the system via the following url:<br>
{{$tenant_data['subdomain'].'.'.config('app.central_domain')}}<br>
user: {{$tenant_data['name']}}<br>
pass: {{$tenant_data['password']}}
</p>
<p> <strong>Step 2: Exploring Your Dashboard</strong> <br>
Once you're logged in, you'll be directed to your personalized dashboard. This is where you'll find all the features and tools tailored to your needs. Take a moment to familiarize yourself with the layout and navigation.</p>
<p> <strong>Step 3: Setting Up Your Profile</strong> <br>
Click on the "Profile" section from the top right corner of the navigation bar and complete your personal details. This information will help us provide you with personalized support and notifications.</p>
<p> <strong>Step 4: Configuring Your Preferences</strong> <br>
Navigate to the "Settings" section to customize your experience. You can set your time zone, currency, date format and lots more from the settings.
</p>
<p> <strong>Step 5: Exploring Features</strong> <br>
We have a comprehensive set of features to enhance your productivity. Take some time to explore our various modules, tools, and integrations.</p>
<p> <strong>Step 6: Need Assistance?</strong> <br>
Our dedicated customer support team is always ready to assist you. If you have any questions, encounter any difficulties, or need guidance, don't hesitate to reach out to us at {{$tenant_data['superadmin_email']}}. We're here to help and ensure you have a seamless experience.</p>
<p>We are excited to embark on this journey together and help you achieve your goals through our platform. We believe you'll find great value in our services, and we look forward to exceeding your expectations.</p>
<p>If you have any further questions or feedback, please don't hesitate to contact us. We're always ready to listen and assist.</p>
<p>Best regards,</p>
<p>{{$tenant_data['superadmin_company_name']}}<br>
{{$tenant_data['superadmin_email']}}</p>