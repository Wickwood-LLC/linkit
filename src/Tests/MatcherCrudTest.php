<?php

/**
 * @file
 * Contains \Drupal\linkit\Tests\MatcherCrudTest.
 */

namespace Drupal\linkit\Tests;
use Drupal\linkit\Entity\Profile;

/**
 * Tests adding, listing, updating and deleting matchers on a profile.
 *
 * @group linkit
 */
class MatcherCrudTest extends LinkitTestBase {

  /**
   * Modules to enable.
   *
   * @TODO: Use a test matchers implementation here?
   *
   * @var array
   */
  public static $modules = ['user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test the overview page.
   */
  function testOverview() {
    $profile = $this->createProfile();

    $this->drupalGet(\Drupal::url('linkit.matchers', [
      'linkit_profile' => $profile->id(),
    ]));
    $this->assertText(t('No matchers added.'));

    $this->assertLinkByHref(\Drupal::url('linkit.matcher.add', [
      'linkit_profile' => $profile->id(),
    ]));
  }

  /**
   * Test adding a matcher to a profile.
   */
  function testAdd() {
    $profile = $this->createProfile();
    $this->drupalGet(\Drupal::url('linkit.matcher.add', [
      'linkit_profile' => $profile->id(),
    ]));

    // We have only enabled the user module, so we will only have one matcher
    // available.
    $this->assertEqual(2, count($this->xpath('//select/option')), 'User matcher is available.');

    $edit = array();
    $edit['plugin'] = 'entity:user';
    $this->drupalPostForm(NULL, $edit, t('Save and continue'));

    // @TODO: How can we test that we are redirected to the edit page? How do we
    // get the plugins uuid?
    //$this->assertUrl(\Drupal::url('linkit.matcher.edit', [
    //  'linkit_profile' => $profile->id(),
    //  'plugin_id' => ???,
    //]));

    $this->drupalGet(\Drupal::url('linkit.matchers', [
      'linkit_profile' => $profile->id(),
    ]));
    $this->assertNoText(t('No matchers added.'));
  }

  /**
   * Test delete a matcher from a profile.
   */
  function testDelete() {
    $profile = $this->createProfile();
    $plugin_uuid = $profile->addMatcher(['id' => 'entity:user']);
    $profile->save();

    // Try delete a matcher that is not attached to the profile.
    $this->drupalGet(\Drupal::url('linkit.matcher.delete', [
      'linkit_profile' => $profile->id(),
      'plugin_instance_id' => 'doesntexists'
    ]));
    $this->assertResponse('404');

    // Go to the delete page, but press cancel.
    $this->drupalGet(\Drupal::url('linkit.matcher.delete', [
      'linkit_profile' => $profile->id(),
      'plugin_instance_id' => $plugin_uuid,
    ]));
    $this->clickLink(t('Cancel'));
    $this->assertUrl(\Drupal::url('linkit.matchers', [
      'linkit_profile' => $profile->id(),
    ]));

    // Delete the matcher from the profile.
    $this->drupalGet(\Drupal::url('linkit.matcher.delete', [
      'linkit_profile' => $profile->id(),
      'plugin_instance_id' => $plugin_uuid,
    ]));

    $this->drupalPostForm(NULL, [], t('Confirm'));
    $this->assertRaw(t('The matcher %plugin has been deleted.', ['%plugin' => 'User']));
    $this->assertUrl(\Drupal::url('linkit.matchers', [
      'linkit_profile' => $profile->id(),
    ]));
    $this->assertText(t('No matchers added.'));

    /** @var \Drupal\linkit\Entity\Profile $updated_profile */
    $updated_profile = Profile::load($profile->id());
    $this->assertFalse($updated_profile->getMatchers()->has($plugin_uuid), 'The user matcher is deleted from the profile');
  }

}