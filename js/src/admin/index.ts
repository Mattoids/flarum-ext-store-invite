import app from 'flarum/admin/app';

app.initializers.add('mattoid/flarum-ext-store-invite', () => {
  app.extensionData.for("mattoid-store-invite")
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
      setting: 'mattoid-store-invite.calm-down-period',
      help: app.translator.trans('mattoid-store-invite.admin.settings.calm-down-period-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.calm-down-period'),
      type: 'number',
      default: ''
    })
    .registerSetting({
      setting: 'mattoid-store-invite.price',
      help: app.translator.trans('mattoid-store-invite.admin.settings.price-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.price'),
      type: 'number',
      default: ''
    })
    .registerSetting({
      setting: 'mattoid-store-invite.mail.title',
      help: app.translator.trans('mattoid-store-invite.admin.settings.mail-title-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.mail-title'),
      type: 'text',
      default: ''
    })
    .registerSetting({
      setting: 'mattoid-store-invite.mail',
      help: app.translator.trans('mattoid-store-invite.admin.settings.mail-help'),
      label: app.translator.trans('mattoid-store-invite.admin.settings.mail'),
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
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-admin-view'),
        permission: 'mattoid-store-invite.group-admin-view',
        allowGuest: true
      }, 'moderate')
});
