import LogoSource from '../../public/images/logo.jpg';

export default function Logo(props){
    return (
        <>
            <img classNames={props.classNames} src={LogoSource} />
        </>
    )
}