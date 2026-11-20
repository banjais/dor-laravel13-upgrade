<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContactEmailRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.default' => 'array',
            'general.email.is_test_mode' => false,
        ]);

        Schema::create('configuration', function (Blueprint $table) {
            $table->id();
            $table->string('configuration_name')->unique();
            $table->text('configuration_value')->nullable();
        });

        DB::table('configuration')->insert([
            ['configuration_name' => 'feedback_cc_to', 'configuration_value' => 'global@example.test'],
            ['configuration_name' => 'no_reply_email', 'configuration_value' => 'noreply@example.test'],
            ['configuration_name' => 'from_name', 'configuration_value' => 'DoR Test'],
            ['configuration_name' => 'absolute_site_path', 'configuration_value' => 'http://localhost'],
            ['configuration_name' => 'relative_site_path', 'configuration_value' => '/'],
            ['configuration_name' => 'site_name', 'configuration_value' => 'DoR Test'],
            ['configuration_name' => 'contact_grs_bcc_enabled', 'configuration_value' => '1'],
            ['configuration_name' => 'contact_grs_bcc_email', 'configuration_value' => 'grms@example.test'],
        ]);

        Schema::create('office', function (Blueprint $table) {
            $table->id();
            $table->string('office_email_address')->nullable();
            $table->string('office_alternative_email')->nullable();
            $table->string('office_subdomain')->nullable();
            $table->string('office_latitude')->nullable();
            $table->string('office_longitude')->nullable();
            $table->text('google_map_embed')->nullable();
            $table->string('office_type')->nullable();
            $table->string('office_attachment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
        });

        Schema::create('office_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('language_id');
            $table->string('office_name')->nullable();
            $table->text('office_address')->nullable();
            $table->string('office_phone_number1')->nullable();
            $table->string('office_phone_number2')->nullable();
        });

        Schema::create('language', function (Blueprint $table) {
            $table->id();
            $table->string('language_code');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
        });

        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->string('page_name');
            $table->text('meta_tags')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('seo_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seo_id');
            $table->unsignedBigInteger('language_id');
            $table->string('page_title')->nullable();
        });

        DB::table('language')->insert([
            ['id' => 1, 'language_code' => 'ne'],
            ['id' => 2, 'language_code' => 'en'],
        ]);

        DB::table('office')->insert([
            [
                'id' => 10,
                'office_subdomain' => 'test-office',
                'office_email_address' => 'office-primary@example.test',
                'office_alternative_email' => 'office-alt@example.test; OFFICE-ALT@example.test',
            ],
            [
                'id' => 20,
                'office_subdomain' => 'home',
                'office_email_address' => 'dg-primary@example.test',
                'office_alternative_email' => 'dg-alt@example.test; office-alt@example.test',
            ],
        ]);

        DB::table('office_detail')->insert([
            [
                'office_id' => 10,
                'language_id' => 1,
                'office_name' => 'Test Office',
            ],
            [
                'office_id' => 20,
                'language_id' => 2,
                'office_name' => 'Head Office',
            ],
        ]);

        $seoId = DB::table('seo')->insertGetId([
            'page_name' => 'feedback',
            'is_active' => 1,
        ]);

        DB::table('seo_detail')->insert([
            'seo_id' => $seoId,
            'language_id' => 1,
            'page_title' => 'Feedback',
        ]);
    }

    public function test_contact_form_routes_to_current_office_with_selected_cc_and_bcc(): void
    {
        $message = \Mockery::mock();

        $message->shouldReceive('from')
            ->once()
            ->with('noreply@example.test', 'DoR Test');

        $message->shouldReceive('to')
            ->once()
            ->with('office-primary@example.test')
            ->andReturnSelf();

        $message->shouldReceive('subject')
            ->once()
            ->with('Feedback form submitted.')
            ->andReturnSelf();

        $message->shouldReceive('cc')
            ->once()
            ->with([
                'office-alt@example.test',
                'visitor-copy@example.test',
            ]);

        $message->shouldReceive('bcc')
            ->once()
            ->with([
                'dg-primary@example.test',
                'dg-alt@example.test',
                'grms@example.test',
            ]);

        $mailer = \Mockery::mock();

        $mailer->shouldReceive('send')
            ->once()
            ->with(
                'emails.email',
                \Mockery::type('array'),
                \Mockery::type('callable')
            )
            ->andReturnUsing(function ($view, $data, $callback) use ($message) {
                $callback($message);

                return new \stdClass();
            });

        Mail::shouldReceive('mailer')
            ->once()
            ->withNoArgs()
            ->andReturn($mailer);

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'cc_email' => 'visitor-copy@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'Contact routing test',
                'captcha' => '12345',
                'contact_bcc' => [
                    'director_general',
                    'grievance_hearing_officer',
                ],
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas('message', 'Your feedback is sent successfully.');

        $this->assertDatabaseCount('configuration', 8);
    }

    public function test_disabled_grms_bcc_is_excluded_while_selected_dg_bcc_is_sent(): void
    {
        DB::table('configuration')
            ->where('configuration_name', 'contact_grs_bcc_enabled')
            ->update(['configuration_value' => '0']);

        $message = \Mockery::mock();
        $message->shouldReceive('from')->once()->with('noreply@example.test', 'DoR Test');
        $message->shouldReceive('to')->once()->with('office-primary@example.test')->andReturnSelf();
        $message->shouldReceive('subject')->once()->with('Feedback form submitted.')->andReturnSelf();
        $message->shouldReceive('cc')->once()->with(['office-alt@example.test']);
        $message->shouldReceive('bcc')->once()->with([
            'dg-primary@example.test',
            'dg-alt@example.test',
        ]);

        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')->once()
            ->with('emails.email', \Mockery::type('array'), \Mockery::type('callable'))
            ->andReturnUsing(function ($view, $data, $callback) use ($message) {
                $callback($message);
                return new \stdClass();
            });

        Mail::shouldReceive('mailer')->once()->withNoArgs()->andReturn($mailer);

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'GRMS disabled routing test',
                'captcha' => '12345',
                'contact_bcc' => ['director_general', 'grievance_hearing_officer'],
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas('message', 'Your feedback is sent successfully.');
    }

    public function test_no_bcc_is_sent_when_no_contact_bcc_option_is_selected(): void
    {
        $message = \Mockery::mock();
        $message->shouldReceive('from')->once()->with('noreply@example.test', 'DoR Test');
        $message->shouldReceive('to')->once()->with('office-primary@example.test')->andReturnSelf();
        $message->shouldReceive('subject')->once()->with('Feedback form submitted.')->andReturnSelf();
        $message->shouldReceive('cc')->once()->with(['office-alt@example.test']);
        $message->shouldReceive('bcc')->never();

        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')->once()
            ->with('emails.email', \Mockery::type('array'), \Mockery::type('callable'))
            ->andReturnUsing(function ($view, $data, $callback) use ($message) {
                $callback($message);
                return new \stdClass();
            });

        Mail::shouldReceive('mailer')->once()->withNoArgs()->andReturn($mailer);

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'No BCC routing test',
                'captcha' => '12345',
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas('message', 'Your feedback is sent successfully.');
    }

    public function test_contact_form_test_mode_sends_only_to_configured_test_recipient(): void
    {
        config([
            'general.email.is_test_mode' => true,
            'general.email.test_email_to' => 'test-recipient@example.test',
            'general.email.test_email_from' => 'test-sender@example.test',
        ]);

        $message = \Mockery::mock();
        $message->shouldReceive('from')->once()->with('test-sender@example.test', 'DoR Test');
        $message->shouldReceive('to')->once()->with('test-recipient@example.test')->andReturnSelf();
        $message->shouldReceive('subject')->once()->with('Feedback form submitted.')->andReturnSelf();
        $message->shouldReceive('cc')->never();
        $message->shouldReceive('bcc')->never();

        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')->once()
            ->with('emails.email', \Mockery::type('array'), \Mockery::type('callable'))
            ->andReturnUsing(function ($view, $data, $callback) use ($message) {
                $callback($message);
                return new \stdClass();
            });

        Mail::shouldReceive('mailer')->once()->withNoArgs()->andReturn($mailer);

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'Contact test mode routing test',
                'captcha' => '12345',
                'cc_email' => 'visitor-copy@example.test',
                'contact_bcc' => ['director_general', 'grievance_hearing_officer'],
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas('message', 'Your feedback is sent successfully.');
    }

    public function test_contact_form_shows_failure_when_mailer_returns_null(): void
    {
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->andReturn(null);

        Mail::shouldReceive('mailer')
            ->once()
            ->withNoArgs()
            ->andReturn($mailer);

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'Mailer null failure test',
                'captcha' => '12345',
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas(
                'message',
                'Feedback could not be sent. Please contact the concerned office.'
            );
    }

    public function test_contact_form_shows_failure_when_mail_transport_throws_exception(): void
    {
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->andThrow(new \Symfony\Component\Mailer\Exception\TransportException(
                'SMTP transport unavailable'
            ));

        Mail::shouldReceive('mailer')
            ->once()
            ->withNoArgs()
            ->andReturn($mailer);

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'SMTP transport failure test',
                'captcha' => '12345',
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas(
                'message',
                'Feedback could not be sent. Please contact the concerned office.'
            );
    }

    public function test_contact_form_does_not_send_when_office_primary_email_is_empty(): void
    {
        \DB::table('office')
            ->where('office_subdomain', 'test-office')
            ->update(['office_email_address' => '']);

        Mail::shouldReceive('mailer')->never();

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'Empty office email test',
                'captcha' => '12345',
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas(
                'message',
                'Feedback could not be sent. Please contact the concerned office.'
            );
    }

    public function test_contact_form_does_not_send_when_office_primary_email_is_invalid(): void
    {
        \DB::table('office')
            ->where('office_subdomain', 'test-office')
            ->update(['office_email_address' => 'invalid-office-email']);

        Mail::shouldReceive('mailer')->never();

        $this->from('/test-office/contact')
            ->withSession(['phrase' => '12345'])
            ->post('/test-office/feedback', [
                'form_context' => 'contact',
                'full_name' => 'Test Visitor',
                'email_address' => 'visitor@example.test',
                'contact_number' => '9800000000',
                'address' => 'Kathmandu',
                'message' => 'Invalid office email test',
                'captcha' => '12345',
            ])
            ->assertRedirect('/test-office/contact')
            ->assertSessionHas(
                'message',
                'Feedback could not be sent. Please contact the concerned office.'
            );
    }
}
