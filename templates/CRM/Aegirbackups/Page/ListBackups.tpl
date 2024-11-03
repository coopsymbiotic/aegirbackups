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
          <td>{$row.size|crmNumberFormat}</td>
          <td><a href="{crmURL p="civicrm/admin/backups" q="op=download&id=`$row.id`"}">{ts}Download{/ts}</a></td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {else}
    <p>{ts}No recent backups found.{/ts}</p>
  {/if}

  <h3>{ts}System Statistics{/ts}</h3>

  <ul>
    <li>{ts 1=$free_disk_space|crmNumberFormat}Free disk space: %1 bytes{/ts}</li>
    <li>{ts 1=$files_size|crmNumberFormat}Files usage: %1 bytes{/ts}</li>
    <li>{ts 1=$db_size|crmNumberFormat}Database size: %1 bytes{/ts}</li>
    <li>{ts 1=$pwd}Current Working Directory: %1{/ts}</li>
  </ul>

  <div class="action-link">
    <a class="button" href="{crmURL p="civicrm/admin/backups" q="op=new"}">{ts}Create new backup{/ts}</a>
  </div>
</div>
