import app from 'flarum/forum/app';

import Component from "flarum/common/Component";
import LinkButton from "flarum/common/components/LinkButton";
import Button from "flarum/common/components/Button";

export default class MyInviteItem extends Component {

  private buttonName: string = 'expand'
  private class: string = ''
  private params: object = {}
  private inviteData: object = {}

  oninit(vnode) {
    super.oninit(vnode);

    this.inviteData = this.attrs.item.attributes
  }

  view() {
    let confirm = m('div', [
      m('div.spacing', [
        // m('label', app.translator.trans('mattoid-store-invite.lib.item-confirm-user') + ': '),
        // m('span', LinkButton.component({
        //   href: '/u/' + this.inviteData.confirmUser
        // }, <img className="Invite-Icon" src={this.inviteData.confirmUserImg}/>)),
        // m('span', LinkButton.component({
        //   href: '/u/' + this.inviteData.confirmUser
        // }, this.inviteData.confirmUser)),
        // m('span.horizontal-spacing', ' | '),
        m('label', app.translator.trans('mattoid-store-invite.lib.item-confirm-status') + ': '),
        m('span' + (this.inviteData.status == 1 ? '.green' : this.inviteData.status == 2 ? '.red' :'.default'), this.inviteData.status == 1 ? '通过' : this.inviteData.status == 2 ? '拒绝' : '未审核'),
        m('span.horizontal-spacing', ' | '),
        m('label', app.translator.trans('mattoid-store-invite.lib.item-confirm-time') + ': '),
        m('span', this.inviteData.confirmTime),
        m('span.horizontal-spacing', ' | '),
        m('label', app.translator.trans('mattoid-store-invite.lib.item-invite-code') + ': '),
        m('span', this.inviteData.inviteCode),
      ]),
      m('div.spacing.fixed', [
        m('label', app.translator.trans('mattoid-store-invite.lib.item-confirm-remark') + ': '),
        m('div', this.inviteData.confirmRemark)
      ]),
    ])
    if (this.inviteData.status == 0) {
      confirm = m('div', [
        m('div.spacing', [
          m('label', app.translator.trans('mattoid-store-invite.lib.item-confirm-status') + ': '),
          m('span', this.inviteData.status == 1 ? '通过' : this.inviteData.status == 2 ? '拒绝' : '未审核'),
        ])
      ])
    }

    return m('div.Invite-Body' + this.class , [
      m('.Form-group', [
        m('div', [
          m('div.rightAligned', Button.component(
            {
              onclick: () => {
                if (this.buttonName == 'expand') {
                  this.buttonName = 'retract'
                  this.class = ' .my.extended'
                  if (this.inviteData.status == 0) {
                    this.class = ' .my.confirm.extended'
                  }
                } else {
                  this.buttonName = 'expand'
                  this.class = ''
                }
              }
            },
            app.translator.trans('mattoid-store-invite.lib.item-button-' + this.buttonName)
          ))
        ]),
        m('div.spacing', [
          m('label', app.translator.trans('mattoid-store-invite.lib.item-user') + ': '),
          m('span', LinkButton.component({
            href: '/u/' + this.inviteData.user
          }, <img className="Invite-Icon" src={this.inviteData.userImg}/>)),
          m('span', LinkButton.component({
            href: '/u/' + this.inviteData.user
          }, this.inviteData.user)),
          m('span.horizontal-spacing', ' | '),
          m('label', app.translator.trans('mattoid-store-invite.lib.item-email') + ': '),
          m('span', this.inviteData.email),
          m('span.horizontal-spacing', ' | '),
          m('label', app.translator.trans('mattoid-store-invite.lib.item-username') + ': '),
          m('span', this.inviteData.username)
        ]),
        m('div.spacing', [
          m('label', app.translator.trans('mattoid-store-invite.lib.item-link') + ': '),
          m('span', LinkButton.component({
            href: this.inviteData.link,
            target: '_blank'
          }, this.inviteData.link)),
        ]),
        m('div.spacing', [
          m('label', app.translator.trans('mattoid-store-invite.lib.item-link-2') + ': '),
          m('span', LinkButton.component({
            href: this.inviteData.link2,
            target: '_blank'
          }, this.inviteData.link2))
        ]),
        m('div.spacing.fixed', [
          m('label', app.translator.trans('mattoid-store-invite.lib.item-apply-rmark') + ': '),
          m('span', this.inviteData.applyRemark),
        ]),
        confirm
      ])
    ])
  }
}
