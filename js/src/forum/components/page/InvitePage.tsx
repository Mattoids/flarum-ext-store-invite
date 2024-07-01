import app from 'flarum/forum/app';

import IndexPage from "flarum/forum/components/IndexPage";
import listItems from 'flarum/common/helpers/listItems';
import Stream from "flarum/common/utils/Stream";
import Mithril from 'mithril';
import Select from "flarum/common/components/Select";
import InviteItem from "../component/InviteItem";

export default class InvitePage<CustomAttrs> extends IndexPage {

  private status = Stream()
  private query = Stream()
  private inviteList: any = []
  private moreResults: boolean = false

  oncreate(vnode: Mithril.VnodeDOM<CustomAttrs, this>) {
    super.oncreate(vnode);

    app.setTitle(app.translator.trans('mattoid-store-invite.forum.invite'));
    app.setTitleCount(0);

    this.status = Stream('0');
    this.query = Stream('');

    this.loadResults();
  }

  view() {
    return (
      <div className="IndexPage">
        <div className="container">
          <div className="sideNavContainer">
            <nav className="IndexPage-nav sideNav">
              <ul>{listItems(this.sidebarItems().toArray())}</ul>
            </nav>
            <div className="StorePage-results sideNavOffset">
              <div className="Post-body">
                <h2 class="BadgeOverviewTitle">{app.translator.trans('mattoid-store-invite.forum.invite')}</h2>
                <div>
                  <div className="Invite-Input">
                    <Select
                      style="width: 150px"
                      value={this.status()}
                      disabled={this.loading}
                      options={{
                        '-1': app.translator.trans('mattoid-store-invite.lib.item-status-all'),
                        '0': app.translator.trans('mattoid-store-invite.lib.item-status-confirm'),
                        '1': app.translator.trans('mattoid-store-invite.lib.item-status-adopt'),
                        '2': app.translator.trans('mattoid-store-invite.lib.item-status-refuse'),
                      }}
                      onchange={(e) => {
                        this.status(e)
                        this.inviteList = []
                        this.loadResults()
                      }}
                    />
                  </div>
                  <div className="Invite-Input">
                    <input required class="FormControl" style="width: 200px" type="text" onblur={() => {
                      this.inviteList = []
                      this.loadResults()
                    }} placeholder={app.translator.trans('mattoid-store-invite.lib.item-query')} bidi={this.query}/>
                  </div>
                </div>
                <div>
                {
                    this.inviteList.map((item) => {
                      if (!item.attributes.hide || app.session.user.attribute('can' + item.attributes.code.slice(0, 1).toUpperCase() + item.attributes.code.slice(1) + 'View')) {
                        return (
                          <div className="">
                            {InviteItem.component({item})}
                          </div>
                        );
                      }
                    })
                  }
                </div>

                {!this.loading && this.inviteList.length === 0 && (
                  <div>
                    <div style="font-size:1.4em;color: var(--muted-more-color);text-align: center;line-height: 100px;">
                      {app.translator.trans('mattoid-store.lib.list-empty')}
                    </div>
                  </div>
                )}

                {!this.loading && this.hasMoreResults() && (
                  <div style="text-align:center;padding:20px">
                    <Button className={'Button Button--primary'} disabled={this.loading} loading={this.loading} onclick={() => this.loadMore()}>
                      {app.translator.trans('mattoid-store.lib.list-load-more')}
                    </Button>
                  </div>
                )}
                </div>
            </div>
          </div>
        </div>
      </div>
    )
  }

  hasMoreResults() {
    return this.moreResults;
  }

  loadMore() {
    this.loading = true;
    this.loadResults(this.inviteList.length);
  }

  parseResults(results) {
    this.moreResults = !!results.payload.links && !!results.payload.links.next;
    [].push.apply(this.inviteList, results.payload.data);
    this.loading = false;
    m.redraw();

    return results;
  }

  loadResults(offset = 0) {
    const filters = {
      query: this.query(),
      status: this.status()
    };

    return app.store
      .find("/store/invite/list", {
        filter:filters,
        page: {
          offset,
        },
      })
      .catch(() => {})
      .then(this.parseResults.bind(this));
  }

}
