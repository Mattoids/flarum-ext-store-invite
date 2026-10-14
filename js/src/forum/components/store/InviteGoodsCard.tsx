import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import InvitePriceModal from './InvitePriceModal';

/**
 * 商店页商品卡片（注册到 app.storeRegistry.item，code = invite）
 * 替换商店插件默认的 StoreItem 展示。
 *
 * 按购买权限展示不同内容：
 *  - 有权限（canInviteView）：显示有权限商品描述
 *  - 无权限：显示无权限商品描述（样式上追加 StoreInvite-GoodsCard--noPermission）
 *
 * 商品介绍区域：鼠标悬停 1 秒后浮层展示完整描述，移出即隐藏。
 *
 * attrs:
 *  - item: 商店商品模型（store 行的 store 模型）
 *  - onSuccess?: 购买成功后由商店页传入的回调（用于局部刷新库存等）
 */
export default class InviteGoodsCard extends Component {
  private storeData: any = {};

  private descTimer: any = null;
  private showDescTip: boolean = false;

  oninit(vnode) {
    super.oninit(vnode);

    this.storeData = this.attrs.item.attributes;
    this.storeData.id = this.attrs.item.id;
  }

  onremove() {
    this.clearDescTimer();
  }

  /**
   * 当前用户是否拥有邀请商品购买权限
   */
  canBuyInvite(): boolean {
    const user = app.session.user;
    if (!user) {
      return false;
    }
    return !!user.attribute('canInviteView');
  }

  /**
   * 按权限取商品描述：后台设置的描述优先，未设置时回退默认文案
   */
  goodsDesc(): string {
    if (this.canBuyInvite()) {
      return app.forum.attribute('inviteGoodsDesc')
        || app.translator.trans('mattoid-store-invite.forum.goods-desc');
    }
    return app.forum.attribute('inviteGoodsNoPermissionDesc')
      || app.translator.trans('mattoid-store-invite.forum.goods-no-permission-desc');
  }

  view() {
    const moneyName = app.forum.attribute('antoinefr-money.moneyname') || '[money]';
    const price = this.storeData.price > 0
      ? moneyName.replace('[money]', this.storeData.price)
      : app.translator.trans('mattoid-store.forum.free');
    const discountPrice = moneyName.replace('[money]', this.storeData.discountPrice);
    const isVideo = this.storeData.icon && this.storeData.icon.slice(-5) === '.webm';
    const canBuy = this.canBuyInvite();

    return (
      <div id={'goods' + this.storeData.id} className={'StoreItem StoreInvite-GoodsCard' + (canBuy ? '' : ' StoreInvite-GoodsCard--noPermission')} onclick={() => this.showDetails()}>
        <div className="itemTitle">
          {this.storeData.title}
        </div>
        <div className="StoreItem-icon">
          <img className="logo-img" src={this.storeData.icon} style={isVideo ? 'display: none' : ''}/>
          <video className="logo-video" autoplay loop muted playsinline style={isVideo ? '' : 'display: none'}>
            <source src={this.storeData.icon} type="video/webm"/>
          </video>
        </div>
        <div className="StoreInvite-GoodsCard-descWrap"
             onmouseenter={() => this.onDescEnter()}
             onmouseleave={() => this.onDescLeave()}>
          <div className="StoreItem-desc StoreInvite-GoodsCard-desc">
            {this.goodsDesc()}
          </div>
          {this.showDescTip ? (
            <div className="StoreInvite-GoodsCard-descTip">
              {this.goodsDesc()}
            </div>
          ) : null}
        </div>
        <div className="StoreItem-footer">
          <div className="StoreItem-priceGroup">
            {this.storeData.discountPrice > 0 && <span className="discount">{price}</span>}
            <span className="price">
              {this.storeData.discountPrice > 0 ? discountPrice : price}
            </span>
          </div>
          <div className="StoreItem-side">
            <span className="StoreItem-stock">
              {app.translator.trans('mattoid-store.lib.item-stock')}: {this.storeData.stock == -99 ? app.translator.trans('mattoid-store.forum.infinite') : this.storeData.stock}
            </span>
            <span className="StoreItem-type">
              {app.translator.trans('mattoid-store.lib.item-type-' + this.storeData.type)}
              {this.storeData.type === 'limit' ? ` (${this.storeData.outtime}${app.translator.trans('mattoid-store.forum.days')})` : ''}
            </span>
          </div>
        </div>
      </div>
    );
  }

  showDetails() {
    if (app.session.user) {
      app.modal.show(InvitePriceModal, {storeData: this.storeData, onSuccess: this.attrs.onSuccess});
    }
  }

  /**
   * 鼠标移入商品介绍区域：1 秒后浮层展示完整描述
   */
  onDescEnter() {
    this.clearDescTimer();
    this.descTimer = setTimeout(() => {
      this.showDescTip = true;
      m.redraw();
    }, 1000);
  }

  /**
   * 鼠标移出商品介绍区域：立即隐藏浮层并取消计时
   */
  onDescLeave() {
    this.clearDescTimer();
    if (this.showDescTip) {
      this.showDescTip = false;
      m.redraw();
    }
  }

  clearDescTimer() {
    if (this.descTimer) {
      clearTimeout(this.descTimer);
      this.descTimer = null;
    }
  }
}
