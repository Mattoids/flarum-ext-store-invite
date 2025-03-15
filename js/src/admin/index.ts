import app from 'flarum/admin/app';
import Stream from "flarum/common/utils/Stream";
import Select from 'flarum/common/components/Select';

app.initializers.add('mattoid/flarum-ext-store-invite', () => {
  let options: any[] = [];

  app.extensionData.for("mattoid-store-invite")
    .registerSetting(function () {
      app.store.all('groups').map((group) => {
        if (group.nameSingular() === 'Guest') {
          return;
        }
        options[group.id()] = group.nameSingular();
      });
    })
    .registerSetting({
      setting: 'mattoid-store-invite.show-index',
      help: app.translator.trans('mattoid-store-invite.admin.settings.show-index-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.show-index'),
      type: 'switch',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.inconsistency.email',
      help: app.translator.trans('mattoid-store-invite.admin.settings.inconsistency-email-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.inconsistency-email'),
      type: 'switch',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.inconsistency.username',
      help: app.translator.trans('mattoid-store-invite.admin.settings.inconsistency-username-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.inconsistency-username'),
      type: 'switch',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.auto.review',
      help: app.translator.trans('mattoid-store-invite.admin.settings.auto-review-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.auto-review'),
      type: 'switch',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.auto.review.username',
      help: app.translator.trans('mattoid-store-invite.admin.settings.auto-review-username-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.auto-review-username'),
      type: 'text',
      default: 'admin'
    })
    .registerSetting({
      setting: 'mattoid-store-invite.invite-calm-down-period',
      help: app.translator.trans('mattoid-store-invite.admin.settings.invite-calm-down-period-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.invite-calm-down-period'),
      type: 'number',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.calm-down-period',
      help: app.translator.trans('mattoid-store-invite.admin.settings.calm-down-period-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.calm-down-period'),
      type: 'number',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.invite-validity-period',
      help: app.translator.trans('mattoid-store-invite.admin.settings.invite-validity-period-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.invite-validity-period'),
      type: 'number',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.price',
      help: app.translator.trans('mattoid-store-invite.admin.settings.price-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.price'),
      type: 'number',
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.post-num',
      help: app.translator.trans('mattoid-store-invite.admin.settings.post-num-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.post-num'),
      type: 'number',
      default: 20
    })
    .registerSetting({
      setting: 'mattoid-store-invite.group',
      help: app.translator.trans('mattoid-store-invite.admin.settings.group-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.group'),
      placeholder: app.translator.trans('mattoid-store-invite.admin.settings.group'),
      type: 'select',
      options: options,
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-store-invite.mail.title',
      help: app.translator.trans('mattoid-store-invite.admin.settings.mail-title-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.mail-title'),
      placeholder: app.translator.trans('mattoid-store-invite.admin.settings.mail-title'),
      type: 'text',
      default: ''
    })
    .registerSetting({
      setting: 'mattoid-store-invite.mail',
      help: app.translator.trans('mattoid-store-invite.admin.settings.mail-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.mail'),
      placeholder: app.translator.trans('mattoid-store-invite.admin.settings.mail-template'),
      type: 'textarea',
      rows: 5
    })
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-view'),
        permission: 'mattoid-store-invite.group-view',
        allowGuest: true
      }, 'view')
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-blacklist-view'),
        permission: 'mattoid-store-invite.group-blacklist-view',
        allowGuest: true
      }, 'view')
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-admin-view'),
        permission: 'mattoid-store-invite.group-admin-view',
        allowGuest: true
      }, 'moderate')
});
