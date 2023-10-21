export default function Dashboard() {
    return (
        <>
            <div className="grid grid-cols-4 items-center gap-x-3 p-3">
                <div className="p-3 bg-indigo-700 hover:bg-indigo-600 cursor-pointer rounded relative h-[100px]">
                    <div className="absolute top-0 left-0 right-0 bg-neutral-800 w-full h-[9px] rounded">.</div>
                    <div className="flex flex-col mt-2">
                        <div className="text-[17px] text-white font-bold mb-2">Müşteri</div>
                        <div className="text-[25px] text-white font-bold">50</div>
                    </div>
                </div>
                <div className="p-3 bg-indigo-700 hover:bg-indigo-600 cursor-pointer rounded relative h-[100px]">
                    <div className="absolute top-0 left-0 right-0 bg-neutral-800 w-full h-[9px] rounded">.</div>
                    <div className="flex flex-col mt-2">
                        <div className="text-[17px] text-white font-bold mb-2">Domain</div>
                        <div className="text-[25px] text-white font-bold">50</div>
                    </div>
                </div>
                <div className="p-3 bg-indigo-700 hover:bg-indigo-600 cursor-pointer rounded relative h-[100px]">
                    <div className="absolute top-0 left-0 right-0 bg-neutral-800 w-full h-[9px] rounded">.</div>
                    <div className="flex flex-col mt-2">
                        <div className="text-[17px] text-white font-bold mb-2">Hosting</div>
                        <div className="text-[25px] text-white font-bold">50</div>
                    </div>
                </div>
                <div className="p-3 bg-indigo-700 hover:bg-indigo-600 cursor-pointer rounded relative h-[100px]">
                    <div className="absolute top-0 left-0 right-0 bg-neutral-800 w-full h-[9px] rounded">.</div>
                    <div className="flex flex-col mt-2">
                        <div className="text-[17px] text-white font-bold mb-2">Kar</div>
                        <div className="text-[25px] text-white font-bold">50.000,25 ₺</div>
                    </div>
                </div>

                <div className="p-3 rounded relative h-full mt-3 col-span-4 text-center text-white">
                    <img src="https://atkhosting.com/images/svg/web_tasarim4.svg" className="rounded p-3 text-center"/>
                </div>
            </div>

        </>
    )
}
