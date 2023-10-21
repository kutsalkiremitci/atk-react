import { memo, useCallback, useContext, useEffect, useRef, useState } from "react"
import { useSelector } from 'react-redux'
import useDomainService from "~/services/domains";
import $notify from '~/components/notify';
// import { toast } from 'react-toastify';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { InputText } from "primereact/inputtext";
import { Button } from "primereact/button";
import { FaTimesCircle } from "react-icons/fa"


export default function DomainPrices() {
    const [domainName, setDomainName] = useState('');
    const [price, setPrice] = useState('');

    const domains = useSelector(state => state.domains)
    const domainService = useDomainService();

    const listDomain = async () => {
        domainService.list({
            pagenation: 5,
            offset: 5
        });
    }
    const addDomain = async () => {
        const response = await domainService.add({
            name: domainName,
            price
        });
        $notify(response)
    }
    const updateDomain = async (opts) => {
        let response = await domainService.update(opts.newData)
        $notify(response)
    }
    const removeDomain = async id => {
        let response = await domainService.remove({ id });
        $notify(response)
    }

    useEffect(() => { listDomain() }, [])

    const openCellEditor = (options, type = "text") => {
        return (
            <InputText
                type={type}
                onChange={e => options.editorCallback(e.target.value)}
                value={options.value}
            />
        );
    }

    const formattedDateTimeBody = (data,args) => {
        let dateTime = data[args.field]

        if(dateTime == null) return "-";
        
        dateTime = new Date(data[args.field]).toLocaleString('tr-TR')

        return (
            <>
                {dateTime}
            </>
        )
    }

  
    const removeDomainTemplate = data => {
        console.log(data)
        return (
            <Button className="text-sm" severity="danger" onClick={() => removeDomain(data.id)}>
                <FaTimesCircle />
            </Button>
        )
    }



    return (
        <>
                <div className="flex gap-x-2 m-2">
                    <InputText placeholder="Domain" defaultValue={domainName} onChange={e => setDomainName(e.target.value)} />
                    <InputText placeholder="Fiyat" defaultValue={price} onChange={e => setPrice(+e.target.value)} />
                    <Button className="text-sm" label="Domain Ekle" severity="success" onClick={addDomain} disabled={!domainName || !price} />
                </div>

                <DataTable
                    className="text-sm"
                    filterDisplay="row"
                    value={domains}
                    rows={10}
                    rowsPerPageOptions={[5, 10, 25, 50, 100, 200]}
                    editMode="row"
                    dataKey="id"
                    onRowEditComplete={opts => updateDomain(opts)}
                    onRowEditInit={e => console.log(e)}
                    stripedRows
                    emptyMessage="Herhangi bir domain bulunamadı"
                    paginator>

                    <Column field="id" header="#" ortable></Column>
                    <Column field="name" header="Domain" editor={(options) => openCellEditor(options)} filter filterPlaceholder="Ara" sortable></Column>
                    <Column field="price" header="Fiyat" editor={(options) => openCellEditor(options, "number")} filter filterPlaceholder="Ara"  sortable></Column>
                    <Column style={{ width: 300 }} field="created_at" header="Oluşturma Tar." body={formattedDateTimeBody} filterPlaceholder="Ara" filter sortable></Column>
                    <Column style={{ width: 300 }} field="updated_at" header="Güncellenme Tar." body={formattedDateTimeBody} filterPlaceholder="Ara"  filter sortable></Column>
                    <Column rowEditor headerStyle={{ width: '10%', minWidth: '8rem' }} header="İşlem" colSpan={0}></Column>
                    <Column body={removeDomainTemplate} header="Sil"></Column>

                    
                </DataTable>

        </>
    )
}
