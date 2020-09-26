
create  view  customers_view as  select a.*,b.area_name
from customers a
left JOIN areas b ON a.cs_fk_area = b.pk_id;


create  view  counter_view as
select a.ctr_current - a.ctr_previous as hour_qy ,
b.cus_index,
b.cs_name,
b.cs_fk_area,
b.cs_mobile,
d.t_debit as money_qy,
a.*,
b.p_enabled,
f.t_credit as discount

from counters a
left JOIN customers b ON a.ctr_customer_id = b.pk_id
left JOIN transactions d ON a.ctr_fk_debit = d.pk_id
left JOIN transactions f ON a.fk_transaction_discount = f.pk_id




create  view  transaction_view as  select a.*,b.cs_name
from transactions a
left JOIN customers b ON a.fk_customer = b.pk_id


create  view  receipt_view as  select a.*,b.cs_name,t.*
from transactions a
left JOIN customers b ON a.fk_customer = b.pk_id
left JOIN transactions t ON a.fk_transaction = t.pk_id
