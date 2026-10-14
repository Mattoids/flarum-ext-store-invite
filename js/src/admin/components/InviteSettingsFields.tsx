import app from 'flarum/admin/app';
import Switch from 'flarum/common/components/Switch';
import Select from 'flarum/common/components/Select';

/**
 * 管理端设置页的分组布局
 *
 * 通过 registerSetting 回调注入 ExtensionPage 渲染，
 * 所有输入框复用页面自带的 setting(key) 流绑定，保存逻辑与官方设置完全一致。
 *
 * 分区顺序：功能开关 → 购买与审核展示 → 冷静期与有效期 → 商品描述 → 邮件模板 → 注册用户组
 *
 * @param page ExtensionPage 实例（registerSetting 回调中的 this）
 */
export default function settingsFields(page) {
  const t = (key: string) => app.translator.trans('mattoid-store-invite.admin.settings.' + key);
  const setting = (key: string) => page.setting('mattoid-store-invite.' + key);
  const boolState = (key: string) => {
    const value = setting(key)();
    return !!value && value !== '0';
  };

  const groupOptions: any = {};
  app.store.all('groups').forEach((group) => {
    if (group.nameSingular() === 'Guest') {
      return;
    }
    groupOptions[group.id()] = group.nameSingular();
  });

  return (
    <div className="StoreInviteAdmin">
      <div className="StoreInviteAdmin-section">{t('section-switch')}</div>
      <div className="StoreInviteAdmin-row StoreInviteAdmin-row--switches">
        <div className="StoreInviteAdminSwitch">
          <Switch state={boolState('show-index')} onchange={setting('show-index')}>
            {t('show-index')}
          </Switch>
          <div className="helpText">{t('show-index-help')}</div>
        </div>
        <div className="StoreInviteAdminSwitch">
          <Switch state={boolState('inconsistency.email')} onchange={setting('inconsistency.email')}>
            {t('inconsistency-email')}
          </Switch>
          <div className="helpText">{t('inconsistency-email-help')}</div>
        </div>
        <div className="StoreInviteAdminSwitch">
          <Switch state={boolState('inconsistency.username')} onchange={setting('inconsistency.username')}>
            {t('inconsistency-username')}
          </Switch>
          <div className="helpText">{t('inconsistency-username-help')}</div>
        </div>
        <div className="StoreInviteAdminSwitch">
          <Switch state={boolState('auto.review')} onchange={setting('auto.review')}>
            {t('auto-review')}
          </Switch>
          <div className="helpText">{t('auto-review-help')}</div>
        </div>
      </div>
      <div className="StoreInviteAdminField StoreInviteAdminField--autoReviewUser">
        <label>{t('auto-review-username')}</label>
        <div className="helpText">{t('auto-review-username-help')}</div>
        <input className="FormControl" type="text" bidi={setting('auto.review.username')}/>
      </div>

      <div className="StoreInviteAdmin-section">{t('section-buy')}</div>
      <div className="StoreInviteAdmin-row StoreInviteAdmin-row--3">
        <div className="StoreInviteAdminField">
          <label>{t('price')}</label>
          <div className="helpText">{t('price-help')}</div>
          <input className="FormControl" type="number" min={0} bidi={setting('price')}/>
        </div>
        <div className="StoreInviteAdminField">
          <label>{t('post-num')}</label>
          <div className="helpText">{t('post-num-help')}</div>
          <input className="FormControl" type="number" min={0} bidi={setting('post-num')}/>
        </div>
        <div className="StoreInviteAdminField">
          <label>{t('group')}</label>
          <div className="helpText">{t('group-help')}</div>
          <Select
            className="FormControl"
            options={groupOptions}
            value={setting('group')() || '3'}
            onchange={setting('group')}
          />
        </div>
      </div>

      <div className="StoreInviteAdmin-section">{t('section-limit')}</div>
      <div className="StoreInviteAdmin-row StoreInviteAdmin-row--3">
        <div className="StoreInviteAdminField">
          <label>{t('invite-calm-down-period')}</label>
          <div className="helpText">{t('invite-calm-down-period-help')}</div>
          <input className="FormControl" type="number" min={0} bidi={setting('invite-calm-down-period')}/>
        </div>
        <div className="StoreInviteAdminField">
          <label>{t('calm-down-period')}</label>
          <div className="helpText">{t('calm-down-period-help')}</div>
          <input className="FormControl" type="number" min={0} bidi={setting('calm-down-period')}/>
        </div>
        <div className="StoreInviteAdminField">
          <label>{t('invite-validity-period')}</label>
          <div className="helpText">{t('invite-validity-period-help')}</div>
          <input className="FormControl" type="number" min={0} bidi={setting('invite-validity-period')}/>
        </div>
      </div>

      <div className="StoreInviteAdmin-section">{t('section-goods')}</div>
      <div className="StoreInviteAdmin-row StoreInviteAdmin-row--2">
        <div className="StoreInviteAdminField">
          <label>{t('goods-desc')}</label>
          <div className="helpText">{t('goods-desc-help')}</div>
          <textarea className="FormControl" rows={3} bidi={setting('goods-desc')}/>
        </div>
        <div className="StoreInviteAdminField">
          <label>{t('goods-no-permission-desc')}</label>
          <div className="helpText">{t('goods-no-permission-desc-help')}</div>
          <textarea className="FormControl" rows={3} bidi={setting('goods-no-permission-desc')}/>
        </div>
      </div>

      <div className="StoreInviteAdmin-section">{t('section-mail')}</div>
      <div className="StoreInviteAdminField">
        <label>{t('mail-title')}</label>
        <div className="helpText">{t('mail-title-help')}</div>
        <input className="FormControl" type="text" bidi={setting('mail.title')}/>
      </div>
      <div className="StoreInviteAdminField">
        <label>{t('mail')}</label>
        <div className="helpText">{t('mail-help')}</div>
        <textarea className="FormControl" rows={5} placeholder={t('mail-template')} bidi={setting('mail')}/>
      </div>
    </div>
  );
}
