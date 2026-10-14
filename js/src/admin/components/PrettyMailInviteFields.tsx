import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';

/**
 * 注入到 fof/pretty-mail 设置页的邀请邮件模板字段
 * 由 store-invite 在管理端 initializer 中挂载到 pretty-mail 自定义页的 templateItems。
 *
 * attrs:
 *  - page: pretty-mail 设置页实例（PrettyMailSettingsPage），
 *          复用其 setting() 流绑定，随页面保存按钮一起提交。
 */
export default class PrettyMailInviteFields extends Component {
  view() {
    const page: any = this.attrs.page;
    const t = (key: string) => app.translator.trans('mattoid-store-invite.admin.settings.' + key);

    return (
      <div className="PrettyMailInviteFields">
        <h3>{t('mail-section-pretty')}</h3>
        <div className="Form-group">
          <label>{t('mail-title-pretty')}</label>
          <div className="helpText">{t('mail-title-help')}</div>
          <input className="FormControl" type="text" bidi={page.setting('mattoid-store-invite.mail.title')}/>
        </div>
        <div className="Form-group">
          <label>{t('mail-pretty')}</label>
          <div className="helpText">{t('mail-help')}</div>
          <textarea className="FormControl" rows={5} placeholder={t('mail-template')} bidi={page.setting('mattoid-store-invite.mail')}/>
        </div>
      </div>
    );
  }
}
