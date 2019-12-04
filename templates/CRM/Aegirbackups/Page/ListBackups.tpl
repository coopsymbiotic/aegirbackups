<div class="crm-content-block crm-block">
  {if !empty($backups)}
    <table>
      <thead>
        <th>Date</th>
        <th>Size</th>
        <th>Actions</th>
      </thead>
      <tbody>
      {foreach from=$backups item=row}
        <tr>
          <td>{$row.date|crmDate:"%Y-%m-%d %H:%m"}</td>
          <td>{$row.size|format_size}</td>
          <td><a href="{crmURL p="civicrm/admin/backups" q="op=download&id=`$row.id`"}">{ts}Download{/ts}</a></td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {else}
    <p>{ts}No recent backups found.{/ts}</p>
  {/if}

  <div class="action-link">
    <a class="button" href="{crmURL p="civicrm/admin/backups" q="op=new"}">{ts}Create new backup{/ts}</a>
  </div>
</div>
