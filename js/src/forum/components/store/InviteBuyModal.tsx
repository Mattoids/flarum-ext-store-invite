import app from 'flarum/forum/app';
import Modal, {IInternalModalAttrs} from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';

/**
 * 邀请商品购买弹窗（注册到 app.storeRegistry.priceModal，code = invite）
 * 替代商店插件默认的 StoreBox 动态表单渲染，由插件自身渲染申请表单。
 *
 * attrs:
 *  - storeData: 商店商品数据（id/price/discountPrice/stock/icon/title/code/className 等）
 *  - onSuccess?: 购买成功回调（由商店页传入，用于局部刷新）
 */
interface IInviteBuyModalAttrs extends IInternalModalAttrs {
  storeData: any;
  onSuccess?: (storeData: any) => void;
}

export default class InviteBuyModal extends Modal<IInviteBuyModalAttrs> {
  private storeData: any = {};

  private email: Stream<string> = Stream('');
  private username: Stream<string> = Stream('');
  private link: Stream<string> = Stream('');
  private link2: Stream<string> = Stream('');
  private applyRemark: Stream<string> = Stream('');

  private errorText: string = '';

  oninit(vnode) {
    super.oninit(vnode);

    this.storeData = this.attrs.storeData;
  }

  title() {
    return this.storeData.title || app.translator.trans('mattoid-store-invite.forum.title');
  }

  className(): string {
    return this.storeData.className || 'store-buy Modal--small';
  }

  content() {
    const canBuy = app.session.user && app.session.user.attribute('canStoreView');

    return (
      <div className="Modal-body">
        {this.errorText ? <div className="Alert Alert--error">{this.errorText}</div> : null}
        <form className="StoreInvite-BuyForm" onsubmit={this.onsubmit.bind(this)}>
          {this.renderInput('email', this.email, 'email', true)}
          {this.renderInput('username', this.username)}
          {this.renderInput('link', this.link, 'text', true)}
          {this.renderInput('link2', this.link2, 'text', false, 'link-2')}

          <div className="Form-group">
            <label>{app.translator.trans('mattoid-store-invite.forum.remark')}</label>
            <div className="helpText">{app.translator.trans('mattoid-store-invite.forum.remark-help')}</div>
            <textarea className="FormControl" value={this.applyRemark()} onchange={(e: InputEvent) => {
              this.applyRemark((e.target as HTMLTextAreaElement).value);
            }} disabled={this.loading}/>
          </div>

          <div className="Form-group center">
            <Button
              type="submit"
              className="Button Button--primary"
              loading={this.loading}
              disabled={!canBuy}
            >
              {canBuy
                ? app.translator.trans('mattoid-store.forum.button')
                : app.translator.trans('mattoid-store.forum.no-permission-buy')}
            </Button>
          </div>
        </form>
      </div>
    );
  }

  renderInput(key: string, stream: Stream<string>, type: string = 'text', required: boolean = false, localeKey?: string) {
    const transKey = localeKey || key;
    return (
      <div className="Form-group">
        <label>
          {app.translator.trans('mattoid-store-invite.forum.' + transKey)}
          {required ? <span className="StoreInvite-required"> *</span> : null}
        </label>
        <div className="helpText">{app.translator.trans('mattoid-store-invite.forum.' + transKey + '-help')}</div>
        <input
          className="FormControl"
          type={type}
          value={stream()}
          onchange={(e: InputEvent) => {
            stream((e.target as HTMLInputElement).value);
          }}
          disabled={this.loading}
        />
      </div>
    );
  }

  onsubmit(event: Event) {
    event.preventDefault();
    if (!app.session.user || !app.session.user.attribute('canStoreView')) {
      return;
    }

    // 与服务端校验保持一致：邮箱与个链必填
    if (!this.email()) {
      this.errorText = app.translator.trans('mattoid-store-invite.forum.error.email-not-exist');
      return;
    }
    if (!this.link()) {
      this.errorText = app.translator.trans('mattoid-store-invite.forum.error.link-not-exist');
      return;
    }

    this.loading = true;
    this.errorText = '';

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/store/buy/goods',
      body: {
        id: this.storeData.id,
        email: this.email(),
        username: this.username(),
        link: this.link(),
        link2: this.link2(),
        applyRemark: this.applyRemark(),
      },
    }).then(
      () => {
        // 局部更新余额，避免整页刷新
        const user = app.session.user;
        const paid = this.storeData.discountPrice > 0 ? this.storeData.discountPrice : this.storeData.price;
        if (user && typeof user.attribute('money') === 'number') {
          user.pushAttributes({money: user.attribute('money') - paid});
        }
        if (this.attrs.onSuccess) {
          this.attrs.onSuccess(this.storeData);
        }
        app.alerts.show({type: 'success'}, app.translator.trans('mattoid-store.forum.error.buy-success'));
        app.modal.close();
      },
      (result) => {
        this.loading = false;
        this.errorText = result?.response?.responseJSON?.errors?.[0]?.detail
          || app.translator.trans('mattoid-store.forum.error.validate-fail');
      }
    );
  }
}
